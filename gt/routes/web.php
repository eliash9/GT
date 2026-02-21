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
        'lembagas' => \App\Models\Lembaga::count(),
        'pjgts' => \App\Models\Pjgt::count(),
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

    // Santri Skill Management (nested)
    Route::middleware(['permission:edit santris'])->group(function () {
        Route::post('santris/{santri}/skills', [\App\Http\Controllers\SantriSkillController::class, 'store'])->name('santri-skills.store');
        Route::put('santris/{santri}/skills/{skill}', [\App\Http\Controllers\SantriSkillController::class, 'update'])->name('santri-skills.update');
        Route::delete('santris/{santri}/skills/{skill}', [\App\Http\Controllers\SantriSkillController::class, 'destroy'])->name('santri-skills.destroy');
    });

    // Santri Management
    Route::get('santris/trash', [\App\Http\Controllers\SantriController::class, 'trash'])->name('santris.trash');
    Route::post('santris/{santri}/restore', [\App\Http\Controllers\SantriController::class, 'restore'])->name('santris.restore')->withTrashed();
    Route::delete('santris/{santri}/force-delete', [\App\Http\Controllers\SantriController::class, 'forceDelete'])->name('santris.force-delete')->withTrashed();
    Route::get('santris/export', [\App\Http\Controllers\SantriController::class, 'export'])->name('santris.export');
    Route::post('santris/import', [\App\Http\Controllers\SantriController::class, 'import'])->name('santris.import');
    Route::resource('santris', \App\Http\Controllers\SantriController::class);

    // Category Management
    Route::resource('categories', \Modules\Category\Presentation\Controllers\CategoryController::class);

    // Lembaga, Wilayah, Pjgt
    Route::get('pjgts/export', [\App\Http\Controllers\PjgtController::class, 'export'])->name('pjgts.export');
    Route::post('pjgts/import', [\App\Http\Controllers\PjgtController::class, 'import'])->name('pjgts.import');
    Route::resource('pjgts', \App\Http\Controllers\PjgtController::class);
    Route::resource('wilayahs', \App\Http\Controllers\WilayahController::class);
    Route::get('lembagas/export', [\App\Http\Controllers\LembagaController::class, 'export'])->name('lembagas.export');
    Route::post('lembagas/import', [\App\Http\Controllers\LembagaController::class, 'import'])->name('lembagas.import');
    Route::get('lembagas/sebaran', [\App\Http\Controllers\LembagaController::class, 'sebaran'])->name('lembagas.sebaran');
    Route::resource('lembagas', \App\Http\Controllers\LembagaController::class);

    Route::middleware(['permission:edit lembagas'])->group(function () {
        // Lembaga Kebutuhan Skill (nested)
        Route::post('lembagas/{lembaga}/kebutuhan', [\App\Http\Controllers\LembagaKebutuhanController::class, 'store'])->name('lembaga-kebutuhan.store');
        Route::put('lembagas/{lembaga}/kebutuhan/{kebutuhan}', [\App\Http\Controllers\LembagaKebutuhanController::class, 'update'])->name('lembaga-kebutuhan.update');
        Route::delete('lembagas/{lembaga}/kebutuhan/{kebutuhan}', [\App\Http\Controllers\LembagaKebutuhanController::class, 'destroy'])->name('lembaga-kebutuhan.destroy');
    });

    // Skill Management
    Route::resource('skills', \App\Http\Controllers\SkillController::class)->middleware('permission:view skills');

    // Tahun PSM / Periode Penugasan
    Route::resource('tahun-psm', \App\Http\Controllers\TahunPsmController::class)->middleware('permission:view tahun-psms');

    // Penugasan Management
    Route::middleware(['permission:view penugasans'])->group(function () {
        Route::post('penugasans/{penugasan}/ubah-status', [\App\Http\Controllers\PenugasanController::class, 'ubahStatus'])->name('penugasans.ubah-status');
        Route::resource('penugasans', \App\Http\Controllers\PenugasanController::class);
    });

    // Matching (Rekomendasi Penempatan)
    Route::middleware(['permission:view matching'])->group(function () {
        Route::get('matching', [\App\Http\Controllers\MatchingController::class, 'index'])->name('matching.index');
        Route::post('matching/assign', [\App\Http\Controllers\MatchingController::class, 'assign'])->name('matching.assign');
    });

    // Settings
    Route::get('/settings', [\Modules\Setting\Presentation\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\Modules\Setting\Presentation\Controllers\SettingController::class, 'update'])->name('settings.update');
    Route::get('/activity-log', [\Modules\Setting\Presentation\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index');
});

require __DIR__ . '/auth.php';
