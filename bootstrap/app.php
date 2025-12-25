<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        // Refresh event lifecycle/registration statuses hourly
        $schedule->command('events:refresh-statuses')->hourly();
        
        // Update facility status every minute
        $schedule->command('facilities:update-status')->everyMinute();
        
        // Update facility status after maintenance (daily)
        $schedule->command('app:update-facility-status-after-maintenance')->daily();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'profile.complete' => \App\Http\Middleware\EnsureProfileIsComplete::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle custom equipment exceptions
        $exceptions->render(function (\App\Exceptions\EquipmentNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'equipment_id' => $e->getEquipmentId(),
                ], 404);
            }
            return redirect()->route('inventory.index')
                ->with('error', $e->getMessage());
        });

        $exceptions->render(function (\App\Exceptions\InsufficientQuantityException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'requested_quantity' => $e->getRequestedQuantity(),
                    'available_quantity' => $e->getAvailableQuantity(),
                ], 422);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        });

        $exceptions->render(function (\App\Exceptions\EquipmentStatusException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'current_status' => $e->getCurrentStatus(),
                    'required_status' => $e->getRequiredStatus(),
                ], 422);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        });
    })->create();
