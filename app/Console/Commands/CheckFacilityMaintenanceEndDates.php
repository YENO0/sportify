<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FacilityMaintenance;
use App\Models\Facility;

class CheckFacilityMaintenanceEndDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facility:check-maintenance-end-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for ended facility maintenance periods and reverts facility status to Active if no other maintenance is ongoing.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for ended facility maintenance periods...');

        // Get all facility maintenance records whose end_date has passed
        $endedMaintenances = FacilityMaintenance::where('end_date', '<', now())->get();

        foreach ($endedMaintenances as $maintenance) {
            $facility = $maintenance->facility;

            // Check if there are any other ongoing or future maintenance records for this facility
            $hasOngoingMaintenance = FacilityMaintenance::where('facility_id', $facility->id)
                ->where('end_date', '>=', now()) // Ongoing or future maintenance
                ->exists();

            // If no other ongoing maintenance and the facility status is 'Maintenance', revert to 'Active'
            if (!$hasOngoingMaintenance && $facility->status === 'Maintenance') {
                $facility->status = 'Active';
                $facility->save();
                $this->info("Facility '{$facility->name}' status reverted to Active.");
            }
        }

        $this->info('Maintenance end date check complete.');
    }
}