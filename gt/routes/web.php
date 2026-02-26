<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
       // 'canRegister' => Route::has('register'),
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

    // Laporan per periode Hijriah (period_month 1-10, period_year = tahun Hijriah)
    // Ambil tahun Hijriah terkini dari data yang ada, fallback 1446
    $hijriYear = (int) (\App\Models\Report::max('period_year') ?? 1446);

    $periodLabels = [
        1  => "Syawal - Dzulqa'dah",
        2  => "Dzulqa'dah - Dzulhijjah",
        3  => "Dzulhijjah - Muharam",
        4  => "Muharam - Safar",
        5  => "Safar - Rabi'ul Awal",
        6  => "Rabi'ul Awal - Rabi'ul Akhir",
        7  => "Rabi'ul Akhir - Jumadil Awal",
        8  => "Jumadil Awal - Jumadil Akhir",
        9  => "Jumadil Akhir - Rajab",
        10 => "Rajab - Sya'ban",
    ];

    $monthlyReportsRaw = \App\Models\Report::selectRaw('period_month as month, report_type, COUNT(*) as count')
        ->where('period_year', $hijriYear)
        ->whereIn('report_type', ['gt', 'pjgt'])
        ->groupBy('period_month', 'report_type')
        ->orderBy('period_month')
        ->get();

    $gtChart   = [];
    $pjgtChart = [];
    for ($i = 1; $i <= 10; $i++) {
        $gtChart[]   = $monthlyReportsRaw->where('month', $i)->where('report_type', 'gt')->first()->count   ?? 0;
        $pjgtChart[] = $monthlyReportsRaw->where('month', $i)->where('report_type', 'pjgt')->first()->count ?? 0;
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
            'reports_trend' => ['gt' => $gtChart, 'pjgt' => $pjgtChart],
            'santri_status' => $santriStatusChart,
        ],
        'hijriYear'    => $hijriYear,
        'periodLabels' => array_values($periodLabels),
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

    // Seleksi Management
    Route::get('seleksi', [\App\Http\Controllers\SeleksiController::class, 'index'])->name('seleksi.index');
    Route::post('seleksi/mass-update', [\App\Http\Controllers\SeleksiController::class, 'massUpdate'])->name('seleksi.mass-update');
    Route::get('seleksi/rekap', [\App\Http\Controllers\SeleksiController::class, 'rekap'])->name('seleksi.rekap');

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
    Route::resource('skills', \App\Http\Controllers\SkillController::class);

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

    // Reports
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/analytics', [\App\Http\Controllers\Admin\ReportController::class, 'analytics'])->name('reports.analytics');
    Route::get('reports/analytics/data', [\App\Http\Controllers\Admin\ReportController::class, 'analyticsData'])->name('reports.analytics.data');
    Route::get('reports/targets', [\App\Http\Controllers\Admin\ReportController::class, 'getTargets'])->name('reports.targets');
    Route::get('reports/create', [\App\Http\Controllers\Admin\ReportController::class, 'create'])->name('reports.create');
    Route::post('reports/init', [\App\Http\Controllers\Admin\ReportController::class, 'init'])->name('reports.init');
    Route::resource('reports', \App\Http\Controllers\Admin\ReportController::class)->except(['create', 'show', 'index']);

    // Settings
    Route::resource('report-categories', \App\Http\Controllers\Admin\ReportCategoryController::class);
    Route::resource('report-questions', \App\Http\Controllers\Admin\ReportQuestionController::class);

    // Attendance Sessions
    Route::post('attendance-sessions/{attendanceSession}/toggle', [\App\Http\Controllers\Admin\AttendanceSessionController::class, 'toggleStatus'])->name('attendance-sessions.toggle');
    Route::get('attendance-sessions/{attendanceSession}/scanner', [\App\Http\Controllers\Admin\AttendanceSessionController::class, 'scanner'])->name('attendance-sessions.scanner');
    Route::resource('attendance-sessions', \App\Http\Controllers\Admin\AttendanceSessionController::class);

    // Attendance Reports
    Route::get('attendance-reports', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'index'])->name('attendance-reports.index');
    Route::get('attendance-reports/export/excel', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'exportExcel'])->name('attendance-reports.excel');
    Route::get('attendance-reports/export/pdf', [\App\Http\Controllers\Admin\AttendanceReportController::class, 'exportPdf'])->name('attendance-reports.pdf');

    // Attendance Check-in
    Route::post('attendance/check-in', [\App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendance.check-in');

    Route::get('/settings', [\Modules\Setting\Presentation\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\Modules\Setting\Presentation\Controllers\SettingController::class, 'update'])->name('settings.update');
    Route::get('/activity-log', [\Modules\Setting\Presentation\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index');
});

require __DIR__ . '/auth.php';
