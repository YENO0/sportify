<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventJoinedController;
// Home / welcome page
Route::get('/', function () {
    return view('welcome');
});

// Student-facing events list (redirect to approved)
Route::redirect('/events', '/events/approved');

// Committee event list
Route::get('/committee/events', [EventController::class, 'index'])
    ->name('committee.events.index');

Route::get('/events/create', [EventController::class, 'create'])
    ->name('events.create');

Route::post('/events', [EventController::class, 'store'])
    ->name('events.store');

Route::get('/events/{event}/edit', [EventController::class, 'edit'])
    ->name('events.edit');

Route::put('/events/{event}', [EventController::class, 'update'])
    ->name('events.update');

Route::delete('/events/{event}', [EventController::class, 'destroy'])
    ->name('events.destroy');

Route::post('/events/{event}/approve', [EventController::class, 'approve'])
    ->name('events.approve');

Route::post('/events/{event}/reject', [EventController::class, 'reject'])
    ->name('events.reject');

Route::post('/events/{event}/cancel', [EventController::class, 'cancel'])
    ->name('events.cancel');

Route::get('/events/approved', [EventController::class, 'approved'])
    ->name('events.approved');

// Admin routes for event approval
Route::get('/admin/events', [EventController::class, 'adminIndex'])
    ->name('admin.events.index');

// Public event details page (for students)
Route::get('/events/{event}/show', [EventController::class, 'show'])
    ->name('events.show');

// Committee event details + participants
Route::get('/events/{event}/committee', [EventController::class, 'committeeShow'])
    ->name('events.committee.show');

// Committee-focused event details (alternative path)
Route::get('/committee/events/{event}', [EventController::class, 'committeeShow'])
    ->name('committee.events.show');
// Event registrations
Route::post('/events/{event}/register', [EventJoinedController::class, 'store'])
    ->name('events.register');

Route::post('/eventJoined/{eventJoined}/cancel', [EventJoinedController::class, 'cancel'])
    ->name('eventJoined.cancel');

Route::get('/events/{event}/registrations', [EventJoinedController::class, 'index'])
    ->name('events.registrations');
