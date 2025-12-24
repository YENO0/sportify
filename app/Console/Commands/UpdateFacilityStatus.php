<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FacilityMaintenance;
use App\Models\Facility;
use Carbon\Carbon;

class UpdateFacilityStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facilities:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update facility status based on maintenance schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // 1. Start Maintenance
        // Find maintenances that have started and the facility is not yet in 'Maintenance'
        // Or just enforce it.
        $startingMaintenances = FacilityMaintenance::where('start_date', '<=', $now)
            ->where('end_date', '>', $now)
            ->with('facility')
            ->get();

        foreach ($startingMaintenances as $maintenance) {
            if ($maintenance->facility->status !== 'Maintenance') {
                $maintenance->facility->update(['status' => 'Maintenance']);
                $this->info("Set {$maintenance->facility->name} to Maintenance.");
            }
        }

        // 2. End Maintenance
        // Find facilities in 'Maintenance' status where the maintenance has ended
        // And there is no other active maintenance.
        $facilitiesInMaintenance = Facility::where('status', 'Maintenance')->get();

        foreach ($facilitiesInMaintenance as $facility) {
            // Check if there is any ACTIVE maintenance for this facility
            $activeMaintenance = FacilityMaintenance::where('facility_id', $facility->id)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>', $now)
                ->exists();

            if (!$activeMaintenance) {
                // No active maintenance, set back to Operational
                // You might want to check if it was 'Closed' before, but for now default to Operational
                $facility->update(['status' => 'Active']);
                $this->info("Set {$facility->name} to Active.");
            }
        }

        $this->info('Facility statuses updated.');
    }
}