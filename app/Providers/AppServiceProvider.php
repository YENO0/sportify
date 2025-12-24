<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Facility;
use App\Models\FacilityMaintenance;
use App\Observers\FacilityObserver;
use App\Observers\FacilityMaintenanceObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Facility::observe(FacilityObserver::class);
        FacilityMaintenance::observe(FacilityMaintenanceObserver::class);
    }
}
