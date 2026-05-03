<?php

use App\Models\Booking;
use App\Models\Venue;
use App\Models\User;
use App\Models\Slot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

// ─────────────────────────────────────────────────────────────
// PÚBLICO
// ─────────────────────────────────────────────────────────────

Route::get('/', function () {
    // Limitado a 8 para el home — los slots se cargan solo para hoy
    $venues = Venue::with(['city', 'fields.slots' => fn($q) =>
        $q->where('status', 'available')->whereDate('starts_at', today())
    ])->where('status', 'active')->limit(8)->get()
    ->map(function ($v) {
        $v->disponible_hoy  = $v->fields->some(fn($f) => $f->slots->isNotEmpty());
        $v->precio_desde    = $v->fields->flatMap(fn($f) => $f->slots->pluck('unit_price'))->min() ?? 0;
        return $v;
    });
    return view('user.home', compact('venues'));
});

Route::get('/canchas', function () {
    $ubicacion = request('ubicacion');
    $fecha     = request('fecha', today()->toDateString());
    $tipo      = request('tipo');

    $query = Venue::with(['city', 'fields.slots' => fn($q) =>
        $q->where('status', 'available')->whereDate('starts_at', $fecha)
    ])->where('status', 'active');

    if ($ubicacion) {
        $query->where(function($q) use ($ubicacion) {
            $q->whereHas('city', fn($q2) =>
                $q2->where('name', 'like', "%$ubicacion%")
                   ->orWhere('department', 'like', "%$ubicacion%")
            )->orWhere('district', 'like', "%$ubicacion%")
             ->orWhere('name', 'like', "%$ubicacion%");
        });
    }
    if ($tipo) {
        $query->whereHas('fields', fn($q) => $q->where('sport_type', $tipo));
    }

    // Paginado de 12 venues por página
    $venuesPaginated = $query->paginate(12);
    $venues = $venuesPaginated->getCollection()->map(function ($v) {
        $v->disponible   = $v->fields->some(fn($f) => $f->slots->isNotEmpty());
        $v->precio_desde = $v->fields->flatMap(fn($f) => $f->slots->pluck('unit_price'))->min() ?? 0;
        return $v;
    });
    $venuesPaginated->setCollection($venues);

    return view('user.canchas', ['venues' => $venuesPaginated]);
});

Route::get('/canchas/{id}', function ($id) {
    $fecha = request('fecha', today()->toDateString());
    $venue = Venue::with([
        'city',
        'fields.operatingHours',
        'fields.slots' => fn($q) => $q->where('status', 'available')
                                      ->whereDate('starts_at', $fecha)
                                      ->orderBy('starts_at'),
    ])->findOrFail($id);
    return view('user.detalle', compact('venue', 'fecha'));
});

Route::get('/eventos', fn() => view('user.home'));

// ─────────────────────────────────────────────────────────────
// AUTH
// ─────────────────────────────────────────────────────────────

Route::get('/login',  fn() => view('user.login'));
Route::post('/login', function () {
    $credentials = request()->only('email', 'password');

    if (!auth()->attempt($credentials, request()->boolean('remember'))) {
        return back()->withErrors(['email' => 'Correo o contraseña incorrectos.'])->onlyInput('email');
    }

    request()->session()->regenerate();
    $user     = auth()->user();
    $redirect = request('redirect');

    if ($redirect && str_starts_with($redirect, '/')) return redirect($redirect);

    return match($user->role) {
        'admin'   => redirect('/admin'),
        'partner' => redirect('/partner'),
        'staff'   => redirect('/staff'),
        default   => redirect('/'),
    };
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::get('/registro', fn() => view('user.registro'));
Route::post('/registro', function () {
    $data = request()->validate([
        'nombre'   => 'required|string|max:100',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'telefono' => 'nullable|string|max:20',
        'terminos' => 'accepted',
    ], [
        'nombre.required'   => 'El nombre es obligatorio.',
        'email.required'    => 'El correo es obligatorio.',
        'email.unique'      => 'Ese correo ya está registrado.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
        'terminos.accepted' => 'Debés aceptar los términos y condiciones.',
    ]);

    $user = User::create([
        'name'     => $data['nombre'],
        'email'    => $data['email'],
        'password' => $data['password'],
        'phone'    => $data['telefono'] ?? null,
        'role'     => 'user',
    ]);

    auth()->login($user);
    request()->session()->regenerate();

    AuditLog::create([
        'user_id'    => $user->id,
        'actor_role' => 'user',
        'action'     => 'USUARIO_REGISTRADO',
        'payload'    => json_encode(['email' => $user->email]),
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);

    return redirect('/')->with('success', '¡Bienvenido, ' . $user->name . '!');
});
Route::get('/registro-partner', fn() => view('user.registro_partner'));
Route::post('/registro-partner', function () {
    $data = request()->validate([
        'nombre_complejo' => 'required|string|max:150',
        'ciudad'          => 'required|string|max:100',
        'distrito'        => 'required|string|max:100',
        'direccion'       => 'required|string|max:200',
        'canchas'         => 'required|integer|min:1|max:30',
        'tipo'            => 'required|in:futbol5,futbol7,futbol11,mixto',
        'nombre'          => 'required|string|max:100',
        'email'           => 'required|email|unique:users,email',
        'whatsapp'        => 'required|string|max:20',
        'mensaje'         => 'nullable|string|max:1000',
    ], [
        'email.unique' => 'Ya existe una cuenta con ese correo.',
    ]);

    // Crear usuario partner con estado pendiente (sin contraseña — se enviará por email al aprobar)
    $user = User::create([
        'name'     => $data['nombre'],
        'email'    => $data['email'],
        'password' => bcrypt(str_random(32)), // temporal, se resetea al aprobar
        'phone'    => $data['whatsapp'],
        'role'     => 'partner',
    ]);

    // Crear el venue en estado "pending" para que el admin lo apruebe
    $city = City::where('name', 'like', '%' . $data['ciudad'] . '%')
        ->orWhere('slug', strtolower($data['ciudad']))
        ->first();

    Venue::create([
        'user_id'     => $user->id,
        'city_id'     => $city?->id,
        'name'        => $data['nombre_complejo'],
        'slug'        => \Illuminate\Support\Str::slug($data['nombre_complejo'] . '-' . time()),
        'address'     => $data['direccion'],
        'district'    => $data['distrito'],
        'latitude'    => $city?->latitude ?? 0,
        'longitude'   => $city?->longitude ?? 0,
        'status'      => 'pending',
        'description' => $data['mensaje'] ?? null,
        'phone'       => $data['whatsapp'],
    ]);

    AuditLog::create([
        'user_id'    => null,
        'actor_role' => 'guest',
        'action'     => 'PARTNER_SOLICITUD',
        'payload'    => json_encode([
            'nombre'   => $data['nombre'],
            'email'    => $data['email'],
            'complejo' => $data['nombre_complejo'],
            'ciudad'   => $data['ciudad'],
        ]),
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);

    return redirect('/registro-partner/enviado');
});
Route::get('/registro-partner/enviado', fn() => view('user.registro_partner_ok'));
Route::get('/recuperar', fn() => view('user.recuperar'));

// ─────────────────────────────────────────────────────────────
// USUARIO AUTENTICADO
// ─────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    Route::get('/perfil', function () {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)
            ->with('field.venue')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'partidos' => $bookings->whereIn('status', ['completed','checked_in'])->count(),
            'canchas'  => $bookings->pluck('field.venue_id')->unique()->count(),
            'horas'    => $bookings->whereIn('status', ['completed','checked_in'])
                            ->sum(fn($b) => $b->slots()->count()),
        ];

        $reservas_recientes = $bookings->take(3);
        return view('user.perfil', compact('user', 'stats', 'reservas_recientes'));
    });

    Route::get('/reservas', function () {
        $user = Auth::user();

        $allBookings = Booking::where('user_id', $user->id)
            ->with(['field.venue.city', 'slots'])
            ->orderByDesc('created_at')
            ->get();

        // Próxima: la confirmada más cercana en el futuro
        $proxima = $allBookings
            ->where('status', 'confirmed')
            ->filter(fn($b) => $b->slots->where('starts_at', '>=', now())->isNotEmpty())
            ->sortBy(fn($b) => $b->slots->min('starts_at'))
            ->first();

        // Activas futuras (confirmadas, sin contar la próxima)
        $activas = $allBookings
            ->where('status', 'confirmed')
            ->filter(fn($b) => $b->slots->where('starts_at', '>=', now())->isNotEmpty())
            ->when($proxima, fn($c) => $c->where('id', '!=', $proxima->id))
            ->values();

        // Historial (pasadas)
        $historial = $allBookings
            ->whereIn('status', ['completed', 'no_show', 'cancelled'])
            ->values();

        return view('user.reservas', compact('proxima', 'activas', 'historial'));
    });

    Route::get('/reservas/{id}', function ($id) {
        $booking = Booking::where('user_id', Auth::id())
            ->with(['field.venue', 'slots'])
            ->findOrFail($id);
        return view('user.reservas', ['proxima' => $booking, 'activas' => collect(), 'historial' => collect()]);
    });

    // GET para acceso directo / POST para envío seguro desde detalle
    Route::match(['get','post'], '/checkout', function () {
        $slotsParam = request('slots', '');
        $total      = (float) request('total', 0);
        $venueId    = request('venue_id');
        $venue      = $venueId ? Venue::with(['city','fields'])->find($venueId) : null;

        return view('user.checkout', compact('slotsParam', 'total', 'venueId', 'venue'));
    });

});

// ─────────────────────────────────────────────────────────────
// PARTNER
// ─────────────────────────────────────────────────────────────

Route::post('/partner/switch-venue', function () {
    $venueId = request('venue_id');
    $user    = Auth::user();
    if ($user->venues()->where('id', $venueId)->exists()) {
        session(['active_venue_id' => $venueId]);
    }
    return back();
})->middleware('auth')->name('partner.switch-venue');

Route::middleware('auth')->prefix('partner')->group(function () {

    $partnerData = function (array $relations = []) {
        $user     = Auth::user();
        $venues   = $user->venues()->with(array_merge(['city'], $relations))->get();
        $activeId = session('active_venue_id');
        $venue    = $venues->firstWhere('id', $activeId) ?? $venues->first();
        if ($venue) session(['active_venue_id' => $venue->id]);
        return compact('user', 'venues', 'venue');
    };

    Route::get('/', function () use ($partnerData) {
        return view('partner.dashboard', $partnerData(['fields']));
    });

    Route::get('/canchas', function () use ($partnerData) {
        return view('partner.canchas', $partnerData(['fields']));
    });

    Route::get('/horarios', function () use ($partnerData) {
        $data  = $partnerData(['fields.operatingHours']);
        $venue = $data['venue'];
        $canchas_real = $venue ? $venue->fields : collect();
        return view('partner.horarios', array_merge($data, compact('canchas_real')));
    });

    Route::get('/staff', function () use ($partnerData) {
        return view('partner.staff', $partnerData(['staff']));
    });

    Route::get('/reservas', function () use ($partnerData) {
        $data  = $partnerData(['fields']);
        $venue = $data['venue'];

        $reservas = $venue
            ? Booking::whereHas('field', fn($q) => $q->where('venue_id', $venue->id))
                ->with(['user', 'field', 'slots'])
                ->orderByDesc('created_at')
                ->get()
            : collect();

        $resumen = [
            'total_reservas' => $reservas->count(),
            'completadas'    => $reservas->where('status', 'completed')->count(),
            'confirmadas'    => $reservas->where('status', 'confirmed')->count(),
            'noshow'         => $reservas->where('status', 'no_show')->count(),
            'ingresos_hoy'   => $reservas->whereIn('status', ['completed','confirmed'])
                                    ->sum('total_price'),
            'anticipo_hoy'   => $reservas->whereIn('status', ['completed','confirmed'])
                                    ->sum('deposit_amount'),
            'efectivo_hoy'   => $reservas->whereIn('status', ['completed','confirmed'])
                                    ->sum('balance_due'),
        ];

        return view('partner.reservas', array_merge($data, compact('reservas', 'resumen')));
    });

    Route::get('/reservas/nueva', fn() => view('partner.reservas'));
    Route::get('/analitica',      fn() => view('partner.analitica'));
    Route::get('/ingresos',       fn() => view('partner.ingresos'));
});

// ─────────────────────────────────────────────────────────────
// STAFF — acciones que persisten en BD
// ─────────────────────────────────────────────────────────────

// Check-in real: valida QR y actualiza booking + shift_movement
Route::post('/staff/checkin', function () {
    $token   = strtoupper(trim(request('qr_token', '')));
    $user    = Auth::user();

    $booking = Booking::where('qr_token', $token)
        ->where('status', 'confirmed')
        ->with(['field.venue', 'slots'])
        ->first();

    if (!$booking) {
        return response()->json(['ok' => false, 'error' => 'QR no válido o reserva no confirmada'], 404);
    }

    // Registrar check-in
    $booking->update([
        'status'        => 'checked_in',
        'checked_in_at' => now(),
        'staff_id'      => $user->id,
    ]);

    // Registrar en shift_movement si hay turno abierto
    $turno = \App\Models\ShiftLog::where('user_id', $user->id)
        ->whereNull('closed_at')
        ->latest('opened_at')
        ->first();

    if ($turno) {
        \App\Models\ShiftMovement::create([
            'shift_log_id' => $turno->id,
            'booking_id'   => $booking->id,
            'type'         => 'checkin',
            'amount'       => (float) $booking->balance_due,
            'description'  => 'Check-in via QR',
        ]);
    }

    return response()->json([
        'ok'      => true,
        'cliente' => $booking->user?->name,
        'cancha'  => $booking->field?->name,
        'hora'    => $booking->timeRange,
        'saldo'   => $booking->balance_due,
    ]);
})->middleware('auth');

// Registrar presencial real
Route::post('/staff/walkin', function () {
    $user    = Auth::user();
    $fieldId = request('field_id');
    $hora    = request('hora', now()->format('H:00'));
    $monto   = (float) request('monto', 70);
    $nombre  = request('nombre', 'Presencial');

    // Buscar slot disponible
    $slot = \App\Models\Slot::where('field_id', $fieldId)
        ->where('status', 'available')
        ->whereDate('starts_at', today())
        ->where('starts_at', today()->setTimeFromTimeString($hora))
        ->first();

    // Crear booking de presencial
    $booking = Booking::create([
        'user_id'        => $user->id,
        'field_id'       => $fieldId,
        'staff_id'       => $user->id,
        'status'         => 'checked_in',
        'total_price'    => $monto,
        'deposit_amount' => $monto,
        'balance_due'    => 0,
        'platform_fee'   => 0,
        'payment_status' => 'paid',
        'payment_method' => 'efectivo',
        'is_walkin'      => true,
        'notes'          => $nombre,
        'checked_in_at'  => now(),
    ]);

    if ($slot) {
        $slot->update(['status' => 'reserved', 'booking_id' => $booking->id]);
        \DB::table('booking_slots')->insert([
            'booking_id' => $booking->id,
            'slot_id'    => $slot->id,
            'unit_price' => $monto,
        ]);
    }

    // Registrar en turno abierto
    $turno = \App\Models\ShiftLog::where('user_id', $user->id)
        ->whereNull('closed_at')->latest('opened_at')->first();

    if ($turno) {
        \App\Models\ShiftMovement::create([
            'shift_log_id' => $turno->id,
            'booking_id'   => $booking->id,
            'type'         => 'walkin',
            'amount'       => $monto,
            'description'  => "Presencial: $nombre",
        ]);
    }

    return response()->json(['ok' => true, 'booking_id' => $booking->id]);
})->middleware('auth');

Route::get('/staff', function () {
    $user = Auth::user();
    // Obtener el venue del staff
    $venueStaff = $user ? \DB::table('venue_staff')
        ->where('user_id', $user->id)
        ->where('active', true)
        ->first() : null;

    $venue = $venueStaff
        ? Venue::with(['fields'])->find($venueStaff->venue_id)
        : null;

    // Próximas reservas del venue
    $proximas = $venue
        ? Booking::whereHas('field', fn($q) => $q->where('venue_id', $venue->id))
            ->where('status', 'confirmed')
            ->with(['user', 'field', 'slots'])
            ->whereHas('slots', fn($q) => $q->where('starts_at', '>=', now()))
            ->orderBy('created_at')
            ->take(5)
            ->get()
        : collect();

    return view('staff.pwa', compact('user', 'venue', 'proximas'));
});

// ─────────────────────────────────────────────────────────────
// ADMIN
// ─────────────────────────────────────────────────────────────

Route::middleware('auth')->prefix('admin')->group(function () {

    Route::get('/', function () {
        $kpis = [
            'venues_activos' => Venue::where('status', 'active')->count(),
            'usuarios'       => User::where('role', 'user')->count(),
            'reservas_mes'   => Booking::whereMonth('created_at', now()->month)->count(),
            'volumen_mes'    => Booking::whereMonth('created_at', now()->month)->sum('total_price'),
            'pendientes'     => Venue::where('status', 'pending')->count(),
            'no_shows_mes'   => Booking::where('status', 'no_show')->whereMonth('created_at', now()->month)->count(),
        ];
        $partners_pendientes = Venue::where('status', 'pending')->with('owner')->get();
        $top_partners = Venue::where('status', 'active')
            ->with(['city', 'fields'])
            ->withCount(['fields as reservas_count' => fn($q) =>
                $q->whereHas('bookings', fn($b) => $b->whereMonth('created_at', now()->month))
            ])
            ->get()->take(5);

        return view('admin.dashboard', compact('kpis', 'partners_pendientes', 'top_partners'));
    });

    Route::get('/partners', function () {
        $partners = Venue::with(['owner', 'city', 'fields'])
            ->withCount('fields')
            ->orderBy('status')
            ->paginate(20);
        return view('admin.partners', compact('partners'));
    });

    Route::get('/usuarios',   fn() => view('admin.usuarios'));

    // Marca — solo Super Admin (no moderadores)
    Route::get('/marca', function () {
        // Solo el usuario con email @futgo.app y role admin puede acceder
        if (Auth::user()->role !== 'admin' || !str_ends_with(Auth::user()->email, '@futgo.app')) {
            abort(403);
        }
        $settings = \App\Models\SiteSetting::pluck('value', 'key')->toArray();
        return view('admin.marca', compact('settings'));
    });

    Route::post('/marca', function () {
        if (Auth::user()->role !== 'admin' || !str_ends_with(Auth::user()->email, '@futgo.app')) {
            abort(403);
        }

        $campos = ['site_name', 'site_tagline', 'site_color', 'site_email', 'site_phone', 'site_country', 'site_currency'];
        foreach ($campos as $campo) {
            if (request()->has($campo)) {
                \App\Models\SiteSetting::set($campo, request($campo));
            }
        }

        // Subida de imágenes
        foreach (['site_logo', 'site_logo_dark', 'site_favicon'] as $imgField) {
            if (request()->hasFile($imgField)) {
                $file = request()->file($imgField);
                $path = $file->store("brand", 'public');
                \App\Models\SiteSetting::set($imgField, '/storage/' . $path);
            }
        }

        // Registrar en audit_log
        \App\Models\AuditLog::record('MARCA_ACTUALIZADA', null, [
            'campos' => $campos,
            'tiene_logo' => request()->hasFile('site_logo'),
        ]);

        return redirect('/admin/marca')->with('success', 'Configuración de marca guardada correctamente.');
    });
    Route::get('/reservas',   function () {
        $reservas = Booking::with(['user', 'field.venue.city'])
            ->orderByDesc('created_at')
            ->paginate(25);
        $stats = [
            'total_mes'   => Booking::whereMonth('created_at', now()->month)->count(),
            'completadas' => Booking::where('status','completed')->whereMonth('created_at', now()->month)->count(),
            'noshows'     => Booking::where('status','no_show')->whereMonth('created_at', now()->month)->count(),
            'volumen'     => Booking::whereMonth('created_at', now()->month)->sum('total_price'),
        ];
        return view('admin.reservas', compact('reservas', 'stats'));
    });
    Route::get('/disputas',   fn() => view('admin.disputas'));
    Route::get('/fees',       fn() => view('admin.fees'));
    Route::get('/plataforma', fn() => view('admin.plataforma'));
    Route::get('/auditoria',  fn() => view('admin.auditoria'));
});
