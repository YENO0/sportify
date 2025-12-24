<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SportController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\SportTypeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EquipmentBorrowingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;

Route::get('/', function () {
    return view('welcome');
});

// Add this new route
Route::get('/sports', [SportController::class, 'index']);

// Inventory Management Routes
Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('index');
    Route::get('/create', [InventoryController::class, 'create'])->name('create');
    Route::post('/', [InventoryController::class, 'store'])->name('store');
    Route::get('/{id}', [InventoryController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('edit');
    Route::put('/{id}', [InventoryController::class, 'update'])->name('update');
    Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/checkout', [InventoryController::class, 'checkout'])->name('checkout');
    Route::post('/{id}/return', [InventoryController::class, 'return'])->name('return');
});

// Brand Registration Routes
Route::prefix('brands')->name('brands.')->group(function () {
    Route::get('/', [BrandController::class, 'index'])->name('index');
    Route::get('/create', [BrandController::class, 'create'])->name('create');
    Route::post('/', [BrandController::class, 'store'])->name('store');
    Route::get('/{id}', [BrandController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [BrandController::class, 'edit'])->name('edit');
    Route::put('/{id}', [BrandController::class, 'update'])->name('update');
    Route::delete('/{id}', [BrandController::class, 'destroy'])->name('destroy');
});

// Maintenance Routes
Route::prefix('maintenance')->name('maintenance.')->group(function () {
    Route::get('/', [MaintenanceController::class, 'index'])->name('index');
    Route::get('/create', [MaintenanceController::class, 'create'])->name('create');
    Route::post('/', [MaintenanceController::class, 'store'])->name('store');
    Route::post('/{id}/status', [MaintenanceController::class, 'updateStatus'])->name('updateStatus');
});

// Sport Type Management Routes
Route::prefix('sport-types')->name('sport-types.')->group(function () {
    Route::get('/', [SportTypeController::class, 'index'])->name('index');
    Route::get('/create', [SportTypeController::class, 'create'])->name('create');
    Route::post('/', [SportTypeController::class, 'store'])->name('store');
    Route::get('/{id}', [SportTypeController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [SportTypeController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SportTypeController::class, 'update'])->name('update');
    Route::delete('/{id}', [SportTypeController::class, 'destroy'])->name('destroy');
});

// Event Management Routes
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/create', [EventController::class, 'create'])->name('create');
    Route::post('/', [EventController::class, 'store'])->name('store');
    Route::get('/{event}', [EventController::class, 'show'])->name('show');
    Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
    Route::put('/{event}', [EventController::class, 'update'])->name('update');
    Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
    Route::post('/{event}/test-return', [EventController::class, 'testReturn'])->name('testReturn');
    Route::post('/process-automatic-returns', [EventController::class, 'processAutomaticReturns'])->name('processAutomaticReturns');
});

// Equipment Borrowing Routes
Route::prefix('events/{event}/borrowings')->name('equipment-borrowings.')->group(function () {
    Route::get('/create', [EquipmentBorrowingController::class, 'create'])->name('create');
    Route::post('/', [EquipmentBorrowingController::class, 'store'])->name('store');
    Route::delete('/{borrowing}', [EquipmentBorrowingController::class, 'destroy'])->name('destroy');
});
Route::get('/about', function () {
    return view('about');
})->name('about');


// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/captcha-reload', [AuthController::class, 'reloadCaptcha'])->name('captcha.reload');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Password reset
    Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// Authenticated routes
Route::middleware(['auth', 'profile.complete'])->group(function () {
    Route::get('/homepage', [HomeController::class, 'index'])->name('homepage');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        // User Management (CRUD)
        Route::resource('admin/users', UserManagementController::class)->names([
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);

        // Committee management
        Route::get('/admin/committee/create', [CommitteeController::class, 'create'])->name('admin.committee.create');
        Route::post('/admin/committee', [CommitteeController::class, 'store'])->name('admin.committee.store');

        // Example: admin-only route for managing events
        Route::get('/admin/events', function () {
            return 'Admin events management (placeholder)';
        })->name('admin.events');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

