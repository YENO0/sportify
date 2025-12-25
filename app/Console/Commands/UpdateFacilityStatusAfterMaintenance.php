<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Facility;
use App\Models\FacilityMaintenance;
use Carbon\Carbon;

class UpdateFacilityStatusAfterMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-facility-status-after-maintenance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates facility status to "Active" after maintenance period has ended.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for facilities with expired maintenance periods...');

        // Use now() helper which respects APP_TIMEZONE from config
        $now = now();

        $maintenances = FacilityMaintenance::where('end_date', '<', $now)->get();

        foreach ($maintenances as $maintenance) {
            $facility = $maintenance->facility;
            if ($facility->status === 'Maintenance') {
                // Check if there are other ongoing maintenances for the same facility
                $otherMaintenances = FacilityMaintenance::where('facility_id', $facility->id)
                    ->where('end_date', '>', $now)
                    ->exists();

                if (!$otherMaintenances) {
                    $facility->status = 'Active';
                    $facility->save();
                    $this->info("Facility #{$facility->id} status updated to Active.");
                }
            }
        }

        $this->info('Done.');
    }
}
