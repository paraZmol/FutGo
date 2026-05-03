<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Venues activos con al menos un slot disponible hoy
    $venues = \App\Models\Venue::with(['city', 'fields'])
        ->where('status', 'active')
        ->get()
        ->map(function ($venue) {
            $venue->disponible_hoy = $venue->fields->some(
                fn($f) => $f->availableToday()->isNotEmpty()
            );
            return $venue;
        });
    return view('user.home', compact('venues'));
});

Route::get('/canchas', function () {
    $ubicacion = request('ubicacion');
    $fecha     = request('fecha', today()->toDateString());
    $tipo      = request('tipo');

    $query = \App\Models\Venue::with(['city', 'fields.slots' => function ($q) use ($fecha) {
        $q->where('status', 'available')->whereDate('starts_at', $fecha);
    }])->where('status', 'active');

    // Filtro por ciudad/ubicación
    if ($ubicacion) {
        $query->whereHas('city', fn($q) =>
            $q->where('name', 'like', "%$ubicacion%")
              ->orWhere('department', 'like', "%$ubicacion%")
        )->orWhere('district', 'like', "%$ubicacion%")
         ->orWhere('address', 'like', "%$ubicacion%");
    }

    // Filtro por tipo de cancha
    if ($tipo) {
        $query->whereHas('fields', fn($q) => $q->where('sport_type', $tipo));
    }

    $venues = $query->get()->map(function ($venue) use ($fecha) {
        $venue->disponible = $venue->fields->some(
            fn($f) => $f->slots->isNotEmpty()
        );
        $venue->precio_desde = $venue->fields->flatMap(fn($f) =>
            $f->slots->pluck('unit_price')
        )->min() ?? 0;
        return $venue;
    });

    return view('user.canchas', compact('venues'));
});

Route::get('/canchas/{id}', function ($id) {
    $venue = \App\Models\Venue::with([
        'city', 'fields.operatingHours',
        'fields.slots' => fn($q) => $q->where('status', 'available')
                                      ->whereDate('starts_at', today())
                                      ->orderBy('starts_at'),
    ])->findOrFail($id);
    return view('user.detalle', compact('venue'));
});
Route::get('/checkout',  fn() => view('user.checkout'));
Route::get('/recuperar', fn() => view('user.recuperar'));
Route::get('/login',     fn() => view('user.login'));
Route::post('/login', function() {
    $credentials = request()->only('email', 'password');

    if (!auth()->attempt($credentials, request()->boolean('remember'))) {
        return back()->withErrors(['email' => 'Correo o contraseña incorrectos.'])->onlyInput('email');
    }

    request()->session()->regenerate();

    $user     = auth()->user();
    $redirect = request('redirect'); // viene del parámetro ?redirect= en la URL

    if ($redirect && str_starts_with($redirect, '/')) {
        return redirect($redirect);
    }

    return match($user->role) {
        'admin'   => redirect('/admin'),
        'partner' => redirect('/partner'),
        'staff'   => redirect('/staff'),
        default   => redirect('/'),
    };
});
Route::get('/registro',         fn() => view('user.registro'));
Route::post('/registro',        fn() => redirect('/'));
Route::get('/registro-partner', fn() => view('user.registro_partner'));
Route::post('/registro-partner',fn() => redirect('/registro-partner/enviado'));
Route::get('/registro-partner/enviado', fn() => view('user.registro_partner'));
Route::post('/logout', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');
Route::get('/perfil',   fn() => view('user.perfil'));
Route::get('/reservas',              fn() => view('user.reservas'));
Route::get('/reservas/{id}',         fn() => view('user.reservas'));
Route::get('/eventos',               fn() => view('user.home'));
Route::get('/partner/reservas/nueva',fn() => view('partner.reservas'));

// Cambiar venue activo (guarda en sesión)
Route::post('/partner/switch-venue', function () {
    $venueId = request('venue_id');
    $user    = Auth::user();
    // Verificar que el venue pertenece al partner
    if ($user->venues()->where('id', $venueId)->exists()) {
        session(['active_venue_id' => $venueId]);
    }
    return back();
})->middleware('auth')->name('partner.switch-venue');

// Partner — rutas protegidas con venue activo en sesión
Route::middleware('auth')->prefix('partner')->group(function () {

    // Helper: obtiene todos los venues del partner y el venue activo
    $partnerData = function (array $relations = []) {
        $user   = Auth::user();
        $venues = $user->venues()->with(array_merge(['city'], $relations))->get();

        // Si no hay venue en sesión o el de sesión no le pertenece, usa el primero
        $activeId = session('active_venue_id');
        $venue    = $venues->firstWhere('id', $activeId) ?? $venues->first();

        // Actualiza sesión con el venue correcto
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
        return view('partner.horarios', $partnerData(['fields']));
    });
    Route::get('/staff', function () use ($partnerData) {
        return view('partner.staff', $partnerData(['staff']));
    });
    Route::get('/reservas',       fn() => view('partner.reservas'));
    Route::get('/reservas/nueva', fn() => view('partner.reservas'));
    Route::get('/analitica',      fn() => view('partner.analitica'));
    Route::get('/ingresos',       fn() => view('partner.ingresos'));
});

Route::get('/staff', fn() => view('staff.pwa'));

// Admin
Route::get('/admin',             fn() => view('admin.dashboard'));
Route::get('/admin/partners',    fn() => view('admin.partners'));
Route::get('/admin/disputas',    fn() => view('admin.disputas'));
Route::get('/admin/usuarios',    fn() => view('admin.usuarios'));
Route::get('/admin/reservas',    fn() => view('admin.reservas'));
Route::get('/admin/fees',        fn() => view('admin.fees'));
Route::get('/admin/plataforma',  fn() => view('admin.plataforma'));
Route::get('/admin/auditoria',   fn() => view('admin.auditoria'));
