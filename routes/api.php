<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EquipmentApiController;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\EquipmentBorrowingApiController;
use App\Http\Controllers\Api\BrandApiController;
use App\Http\Controllers\Api\SportTypeApiController;
use App\Http\Controllers\Api\MaintenanceApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\StudentController;

Route::prefix('v1')->group(function () {
    // Equipment API
    Route::apiResource('equipment', EquipmentApiController::class)->names('api.v1.equipment');
    Route::post('equipment/{id}/checkout', [EquipmentApiController::class, 'checkout'])->name('api.v1.equipment.checkout');
    Route::post('equipment/{id}/return', [EquipmentApiController::class, 'return'])->name('api.v1.equipment.return');
    
    // Events API
    Route::apiResource('events', EventApiController::class)->names('api.v1.events');
    Route::post('events/{event}/test-return', [EventApiController::class, 'testReturn'])->name('api.v1.events.test-return');
    Route::post('events/process-automatic-returns', [EventApiController::class, 'processAutomaticReturns'])->name('api.v1.events.process-automatic-returns');
    
    // Equipment Borrowing API
    Route::prefix('events/{event}/borrowings')->name('api.v1.events.borrowings.')->group(function () {
        Route::get('/available', [EquipmentBorrowingApiController::class, 'getAvailableEquipment'])->name('available');
        Route::post('/', [EquipmentBorrowingApiController::class, 'store'])->name('store');
        Route::delete('/{borrowing}', [EquipmentBorrowingApiController::class, 'destroy'])->name('destroy');
    });
    
    // Brands API
    Route::apiResource('brands', BrandApiController::class)->names('api.v1.brands');
    
    // Sport Types API
    Route::apiResource('sport-types', SportTypeApiController::class)->names('api.v1.sport-types');
    Route::get('sport-types/active/list', [SportTypeApiController::class, 'getActive'])->name('api.v1.sport-types.active.list');
    
    // Maintenance API
    Route::apiResource('maintenance', MaintenanceApiController::class)->names('api.v1.maintenance');
    Route::post('maintenance/{id}/status', [MaintenanceApiController::class, 'updateStatus'])->name('api.v1.maintenance.status');
    Route::get('maintenance/upcoming/list', [MaintenanceApiController::class, 'getUpcoming'])->name('api.v1.maintenance.upcoming.list');

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/test', function(){
        return ['message'=> 'API is working'];
    });
    
    Route::apiResource('students', StudentController::class);

});
