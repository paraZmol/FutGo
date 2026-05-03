<?php

use App\Models\Booking;
use App\Models\City;
use App\Models\AuditLog;
use App\Models\Transaction;
use App\Models\Venue;
use App\Models\User;
use App\Models\Slot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

    $user = User::forceCreate([
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
    $user = User::forceCreate([
        'name'     => $data['nombre'],
        'email'    => $data['email'],
        'password' => bcrypt(\Illuminate\Support\Str::random(32)), // temporal, se resetea al aprobar
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

    // Crear booking real desde el checkout
    Route::post('/booking/crear', function () {
        $fieldId  = (int) request('field_id');
        $slotIds  = json_decode(request('slot_ids', '[]'), true);
        $metodo   = request('metodo', 'yape');
        $total    = (float) request('total', 0);
        $anticipo = (float) request('anticipo', 0);
        $balance  = round($total - $anticipo, 2);

        if (!$fieldId || empty($slotIds)) {
            return redirect()->back()->withErrors(['error' => 'Datos de reserva incompletos.']);
        }

        // Verificar disponibilidad antes de iniciar la transacción
        $slots = Slot::whereIn('id', $slotIds)->where('status', 'available')->get();
        if ($slots->count() !== count($slotIds)) {
            return redirect()->back()->withErrors(['error' => 'Uno o más horarios ya no están disponibles. Por favor elegí otro turno.']);
        }

        // Envolver todo en una transacción — si algo falla, nada se guarda
        $booking = \DB::transaction(function () use ($fieldId, $slotIds, $metodo, $total, $anticipo, $balance, $slots) {
            $booking = Booking::create([
                'user_id'        => Auth::id(),
                'field_id'       => $fieldId,
                'status'         => 'confirmed',
                'total_price'    => $total,
                'deposit_amount' => $anticipo,
                'balance_due'    => $balance,
                'platform_fee'   => 0,
                'payment_status' => 'paid',
                'payment_method' => $metodo,
                'is_walkin'      => false,
            ]);

            foreach ($slots as $slot) {
                \DB::table('booking_slots')->insert([
                    'booking_id' => $booking->id,
                    'slot_id'    => $slot->id,
                    'unit_price' => $slot->unit_price,
                ]);
                $slot->update(['status' => 'reserved', 'booking_id' => $booking->id]);
            }

            Transaction::create([
                'booking_id'     => $booking->id,
                'amount'         => $anticipo,
                'type'           => 'deposit',
                'payment_method' => $metodo,
                'status'         => 'approved',
                'gateway'        => $metodo,
            ]);

            return $booking;
        });

        return redirect('/reservas')->with('success', '¡Reserva confirmada! Tu QR está listo.');
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

Route::middleware(['auth', 'role:partner,admin'])->prefix('partner')->group(function () {

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
    Route::get('/analitica', function () use ($partnerData) {
        $data  = $partnerData(['fields']);
        $venue = $data['venue'];

        // Bookings del venue activo
        $bookings = $venue
            ? Booking::whereHas('field', fn($q) => $q->where('venue_id', $venue->id))
                ->with(['field', 'slots', 'transactions'])
                ->get()
            : collect();

        // KPIs del mes actual
        $mesActual   = $bookings->filter(fn($b) => $b->created_at->month === now()->month);
        $mesAnterior = $bookings->filter(fn($b) => $b->created_at->month === now()->subMonth()->month);

        $kpis = [
            'ingresos_mes'    => $mesActual->whereIn('status',['confirmed','completed','checked_in'])->sum('total_price'),
            'ingresos_ant'    => $mesAnterior->whereIn('status',['confirmed','completed','checked_in'])->sum('total_price'),
            'reservas_mes'    => $mesActual->count(),
            'reservas_ant'    => $mesAnterior->count(),
            'noshow_mes'      => $mesActual->where('status','no_show')->count(),
            'noshow_ant'      => $mesAnterior->where('status','no_show')->count(),
        ];

        // Ingresos por mes (últimos 6)
        $ingresosPorMes = collect();
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $ing = $bookings
                ->filter(fn($b) => $b->created_at->format('Y-m') === $m->format('Y-m'))
                ->whereIn('status',['confirmed','completed','checked_in'])
                ->sum('total_price');
            $ingresosPorMes->push(['mes' => $m->format('M'), 'total' => (float) $ing]);
        }

        // Horarios más populares
        $horarios = $bookings->flatMap(fn($b) => $b->slots)
            ->groupBy(fn($s) => $s->starts_at->format('H:00'))
            ->map(fn($g, $h) => ['hora' => $h, 'reservas' => $g->count()])
            ->sortByDesc('reservas')->take(6)->values();

        // Canal de reservas
        $totalRes = max($bookings->count(), 1);
        $canales = [
            ['canal' => 'App',        'reservas' => $bookings->where('is_walkin', false)->count()],
            ['canal' => 'Presencial', 'reservas' => $bookings->where('is_walkin', true)->count()],
        ];
        foreach ($canales as &$c) { $c['pct'] = round($c['reservas'] / $totalRes * 100); }

        // Días más activos
        $diasNombres = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
        $diasActivos = $bookings->flatMap(fn($b) => $b->slots)
            ->groupBy(fn($s) => $s->starts_at->dayOfWeek)
            ->map(fn($g, $d) => ['dia' => $diasNombres[$d], 'reservas' => $g->count()])
            ->sortKeys()->values();

        // Top clientes
        $topClientes = $bookings->whereIn('status',['confirmed','completed','checked_in'])
            ->groupBy('user_id')
            ->map(fn($group) => [
                'nombre'   => $group->first()->user?->name ?? 'Jugador',
                'reservas' => $group->count(),
                'gasto'    => $group->sum('total_price'),
            ])
            ->sortByDesc('reservas')->take(5)->values();

        // Incumplidores (no-shows)
        $incumplidores = $bookings->groupBy('user_id')
            ->map(fn($group) => [
                'nombre'  => $group->first()->user?->name ?? 'Jugador',
                'total'   => $group->count(),
                'noshow'  => $group->where('status','no_show')->count(),
            ])
            ->filter(fn($u) => $u['noshow'] > 0)
            ->map(fn($u) => array_merge($u, [
                'tasa'   => round($u['noshow'] / max($u['total'],1) * 100),
                'riesgo' => $u['noshow'] >= 2 ? 'alto' : 'medio',
            ]))
            ->sortByDesc('tasa')->take(5)->values();

        return view('partner.analitica', array_merge($data, compact(
            'kpis','ingresosPorMes','horarios','canales','diasActivos','topClientes','incumplidores'
        )));
    });
    Route::get('/ingresos',       fn() => view('partner.ingresos'));
});

// ─────────────────────────────────────────────────────────────
// STAFF — acciones que persisten en BD
// ─────────────────────────────────────────────────────────────

// Check-in real: valida QR y actualiza booking + shift_movement
Route::middleware(['auth', 'role:staff,admin'])->group(function () {

// Abrir turno — valida que no haya otro turno abierto
Route::post('/staff/turno/abrir', function () {
    $user    = Auth::user();
    $venueId = request('venue_id');

    $turnoAbierto = \App\Models\ShiftLog::where('user_id', $user->id)
        ->whereNull('closed_at')->exists();

    if ($turnoAbierto) {
        return response()->json(['ok' => false, 'error' => 'Ya tenés un turno abierto. Cerralo antes de abrir uno nuevo.'], 422);
    }

    $turno = \App\Models\ShiftLog::create([
        'venue_id'     => $venueId,
        'user_id'      => $user->id,
        'opened_at'    => now(),
        'opening_cash' => (float) request('opening_cash', 0),
    ]);

    return response()->json(['ok' => true, 'turno_id' => $turno->id, 'inicio' => $turno->opened_at->format('H:i')]);
});

// Cerrar turno
Route::post('/staff/turno/cerrar', function () {
    $user  = Auth::user();
    $turno = \App\Models\ShiftLog::where('user_id', $user->id)
        ->whereNull('closed_at')->latest('opened_at')->first();

    if (!$turno) {
        return response()->json(['ok' => false, 'error' => 'No tenés un turno abierto.'], 422);
    }

    $totalMovimientos = $turno->movements()->sum('amount');
    $diferencia       = round($totalMovimientos - (float) request('delivered_cash', $totalMovimientos), 2);

    $turno->update([
        'closed_at'      => now(),
        'expected_cash'  => $totalMovimientos,
        'delivered_cash' => (float) request('delivered_cash', $totalMovimientos),
        'difference'     => $diferencia,
        'notes'          => request('notes'),
    ]);

    return response()->json(['ok' => true, 'diferencia' => $diferencia]);
});

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

    // Verificar turno abierto — sin turno no se puede registrar presencial
    $turno = \App\Models\ShiftLog::where('user_id', $user->id)
        ->whereNull('closed_at')->latest('opened_at')->first();

    if (!$turno) {
        return response()->json(['ok' => false, 'error' => 'No tenés un turno abierto. Abrí tu turno antes de registrar presenciales.'], 422);
    }

    $slot = \App\Models\Slot::where('field_id', $fieldId)
        ->where('status', 'available')
        ->whereDate('starts_at', today())
        ->where('starts_at', today()->setTimeFromTimeString($hora))
        ->first();

    // Todo en una transacción — si algo falla no queda nada a medias
    $booking = \DB::transaction(function () use ($user, $fieldId, $monto, $nombre, $slot, $turno) {
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

        \App\Models\ShiftMovement::create([
            'shift_log_id' => $turno->id,
            'booking_id'   => $booking->id,
            'type'         => 'walkin',
            'amount'       => $monto,
            'description'  => "Presencial: $nombre",
        ]);

        return $booking;
    });

    return response()->json(['ok' => true, 'booking_id' => $booking->id]);
});  // fin grupo staff/checkin + staff/walkin

}); // fin middleware role:staff,admin

Route::middleware(['auth', 'role:staff,admin'])->get('/staff', function () {
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

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

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
    Route::get('/disputas', function () {
        $disputas = \App\Models\Dispute::with(['booking.user', 'booking.field.venue', 'resolver'])
            ->orderBy('estado')
            ->orderByDesc('created_at')
            ->get();
        $stats = [
            'abiertas'  => $disputas->where('estado', 'abierta')->count(),
            'resueltas' => $disputas->where('estado', 'resuelta')->count(),
        ];
        return view('admin.disputas', compact('disputas', 'stats'));
    });

    Route::post('/disputas/{id}/resolver', function ($id) {
        $data = request()->validate([
            'resolucion' => 'required|string|min:10|max:1000',
        ], [
            'resolucion.required' => 'Debés escribir una resolución.',
            'resolucion.min'      => 'La resolución debe tener al menos 10 caracteres.',
            'resolucion.max'      => 'La resolución no puede superar 1000 caracteres.',
        ]);

        $disputa = \App\Models\Dispute::findOrFail($id);

        // Solo disputas abiertas se pueden resolver
        if ($disputa->estado === 'resuelta') {
            return back()->withErrors(['error' => 'Esta disputa ya fue resuelta.']);
        }

        $disputa->update([
            'estado'      => 'resuelta',
            'resolucion'  => $data['resolucion'],
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        AuditLog::record('DISPUTA_RESUELTA', $disputa, [
            'booking_id' => $disputa->booking_id,
            'tipo'       => $disputa->tipo,
            'resolucion' => $data['resolucion'],
        ]);

        return back()->with('success', 'Disputa marcada como resuelta.');
    });
    Route::get('/fees',       fn() => view('admin.fees'));
    Route::get('/plataforma', fn() => view('admin.plataforma'));
    Route::get('/auditoria',  fn() => view('admin.auditoria'));
});
