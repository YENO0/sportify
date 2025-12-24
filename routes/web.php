<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SportController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FacilityTimetableController;
use App\Http\Controllers\FacilityMaintenanceController;
use App\Http\Controllers\NotificationController;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Dev Login Route for Testing
Route::get('/login', function () {
    $user = User::find(1);
    if (!$user) {
        $user = User::factory()->create([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
    Auth::login($user);
    return redirect()->intended('/notifications');
})->name('login');

// Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Bookings Routes
    Route::resource('bookings', BookingController::class);

    // Test Route to generate a notification
    Route::get('/test-notify', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        $facility = \App\Models\Facility::first();
        if (!$facility) {
            $facility = \App\Models\Facility::create([
                'name' => 'Main Hall',
                'description' => 'The main sports hall.',
                'type' => 'Indoor',
                'status' => 'Operational'
            ]);
        }
        $user->notify(new \App\Notifications\SystemFacilityStatusNotification($facility, 'Maintenance'));
        return redirect()->route('notifications.index');
    });
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

// Facility Maintenance Routes
Route::prefix('facilities/maintenance')->name('facilities.maintenance.')->group(function () {
    Route::get('/', [FacilityMaintenanceController::class, 'index'])->name('index');
    Route::get('/create', [FacilityMaintenanceController::class, 'create'])->name('create');
    Route::post('/', [FacilityMaintenanceController::class, 'store'])->name('store');
    Route::get('/{facilityMaintenance}/edit', [FacilityMaintenanceController::class, 'edit'])->name('edit');
    Route::put('/{facilityMaintenance}', [FacilityMaintenanceController::class, 'update'])->name('update');
    Route::delete('/{facilityMaintenance}', [FacilityMaintenanceController::class, 'destroy'])->name('destroy');
});


// Facility Management Routes
Route::resource('facilities', FacilityController::class);
Route::get('facility-photos/{filename}', [FacilityController::class, 'getFacilityPhoto'])->name('facilities.photo');
Route::get('facility-timetable', [FacilityTimetableController::class, 'index'])->name('facilities.timetable');