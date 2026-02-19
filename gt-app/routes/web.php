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
    return Inertia::render('Dashboard', ['stats' => $stats]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Management
    Route::resource('users', \Modules\User\Presentation\Controllers\UserController::class);

    // Role Management
    Route::resource('roles', \Modules\User\Presentation\Controllers\RoleController::class);

    // Product Management
    Route::resource('products', \Modules\Product\Presentation\Controllers\ProductController::class);

    // Category Management
    Route::resource('categories', \Modules\Category\Presentation\Controllers\CategoryController::class);

    // Settings
    Route::get('/settings', [\Modules\Setting\Presentation\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\Modules\Setting\Presentation\Controllers\SettingController::class, 'update'])->name('settings.update');
});

require __DIR__ . '/auth.php';
