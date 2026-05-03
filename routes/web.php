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
    $venues = Venue::with(['city', 'fields.slots' => fn($q) =>
        $q->where('status', 'available')->whereDate('starts_at', today())
    ])->where('status', 'active')->get()
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

    $venues = $query->get()->map(function ($v) {
        $v->disponible   = $v->fields->some(fn($f) => $f->slots->isNotEmpty());
        $v->precio_desde = $v->fields->flatMap(fn($f) => $f->slots->pluck('unit_price'))->min() ?? 0;
        return $v;
    });

    return view('user.canchas', compact('venues'));
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

Route::get('/registro',         fn() => view('user.registro'));
Route::post('/registro',        fn() => redirect('/'));
Route::get('/registro-partner', fn() => view('user.registro_partner'));
Route::post('/registro-partner',fn() => redirect('/registro-partner/enviado'));
Route::get('/registro-partner/enviado', fn() => view('user.registro_partner'));
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

    Route::get('/checkout', function () {
        $slotsParam = request('slots', '');
        $total      = (float) request('total', 0);
        $venueId    = request('venue_id');

        return view('user.checkout', compact('slotsParam', 'total', 'venueId'));
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
// STAFF
// ─────────────────────────────────────────────────────────────

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
            ->get();
        return view('admin.partners', compact('partners'));
    });

    Route::get('/usuarios',   fn() => view('admin.usuarios'));
    Route::get('/reservas',   function () {
        $reservas = Booking::with(['user', 'field.venue.city'])
            ->orderByDesc('created_at')
            ->take(50)
            ->get();
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
