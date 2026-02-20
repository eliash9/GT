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
        'products' => \Modules\Product\Infrastructure\Models\Product::count(),
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

    $productStatus = \Modules\Product\Infrastructure\Models\Product::select('is_active', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
        ->groupBy('is_active')
        ->get();

    $productStatusChart = [
        'active' => $productStatus->where('is_active', 1)->first()->total ?? 0,
        'inactive' => $productStatus->where('is_active', 0)->first()->total ?? 0,
    ];

    return Inertia::render('Dashboard', [
        'stats' => $stats,
        'charts' => [
            'users_trend' => $usersChart,
            'product_status' => $productStatusChart,
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

    // Product Management
    Route::get('products/trash', [\Modules\Product\Presentation\Controllers\ProductController::class, 'trash'])->name('products.trash');
    Route::post('products/{product}/restore', [\Modules\Product\Presentation\Controllers\ProductController::class, 'restore'])->name('products.restore');
    Route::delete('products/{product}/force-delete', [\Modules\Product\Presentation\Controllers\ProductController::class, 'forceDelete'])->name('products.force-delete');
    Route::get('products/export', [\Modules\Product\Presentation\Controllers\ProductController::class, 'export'])->name('products.export');
    Route::post('products/import', [\Modules\Product\Presentation\Controllers\ProductController::class, 'import'])->name('products.import');
    Route::resource('products', \Modules\Product\Presentation\Controllers\ProductController::class);

    // Category Management
    Route::resource('categories', \Modules\Category\Presentation\Controllers\CategoryController::class);

    // Settings
    Route::get('/settings', [\Modules\Setting\Presentation\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\Modules\Setting\Presentation\Controllers\SettingController::class, 'update'])->name('settings.update');
    Route::get('/activity-log', [\Modules\Setting\Presentation\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index');
});

require __DIR__ . '/auth.php';
