<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $stats = [
        'users' => \Modules\User\Infrastructure\Models\User::count(),
        'santris' => \App\Models\Santri::count(),
        'categories' => \Modules\Category\Infrastructure\Models\Category::count(),
    ];

    $monthlyUsers = \Modules\User\Infrastructure\Models\User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

    $usersChart = [];
    for ($i = 1; $i <= 12; $i++) {
        $usersChart[] = $monthlyUsers->has($i) ? $monthlyUsers[$i]->count : 0;
    }

    $santriStatus = \App\Models\Santri::select('status_tugas', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
        ->groupBy('status_tugas')
        ->get();

    $santriStatusChart = [
        'menunggu' => $santriStatus->where('status_tugas', 'Menunggu')->first()->total ?? 0,
        'bertugas' => $santriStatus->where('status_tugas', 'Sedang Bertugas')->first()->total ?? 0,
        'selesai' => $santriStatus->where('status_tugas', 'Selesai')->first()->total ?? 0,
    ];

    return Inertia::render('Dashboard', [
        'stats' => $stats,
        'charts' => [
            'users_trend' => $usersChart,
            'santri_status' => $santriStatusChart,
        ]
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API Tokens
    Route::post('/profile/tokens', [\App\Http\Controllers\ApiTokenController::class, 'store'])->name('profile.tokens.store');
    Route::delete('/profile/tokens/{tokenId}', [\App\Http\Controllers\ApiTokenController::class, 'destroy'])->name('profile.tokens.destroy');

    // User Management
    Route::get('users/export', [\Modules\User\Presentation\Controllers\UserController::class, 'export'])->name('users.export');
    Route::post('users/import', [\Modules\User\Presentation\Controllers\UserController::class, 'import'])->name('users.import');
    Route::resource('users', \Modules\User\Presentation\Controllers\UserController::class);

    // Role Management
    Route::resource('roles', \Modules\User\Presentation\Controllers\RoleController::class);

    // Santri Management
    Route::resource('santris', \App\Http\Controllers\SantriController::class);

    // Category Management
    Route::resource('categories', \Modules\Category\Presentation\Controllers\CategoryController::class);

    // Settings
    Route::get('/settings', [\Modules\Setting\Presentation\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\Modules\Setting\Presentation\Controllers\SettingController::class, 'update'])->name('settings.update');
    Route::get('/activity-log', [\Modules\Setting\Presentation\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index');
});

require __DIR__ . '/auth.php';
