<?php

namespace App\Observers;

use App\Models\FacilityMaintenance;
use App\Models\Facility;
use App\Models\User;
use App\Notifications\FacilityMaintenanceNotification;
use Illuminate\Support\Facades\Notification;

class FacilityMaintenanceObserver
{
    /**
     * Handle the FacilityMaintenance "created" event.
     */
    public function created(FacilityMaintenance $facilityMaintenance): void
    {
        $facility = $facilityMaintenance->facility;

        // Send notification to all users
        $users = User::all();
        Notification::send($users, new FacilityMaintenanceNotification($facilityMaintenance));

        // Only set to Maintenance if the start date is reached (now or past)
        // and it's not already Emergency Closure
        if ($facility->status !== 'Emergency Closure' && $facilityMaintenance->start_date <= now()) {
            $facility->status = 'Maintenance';
            $facility->save();
        }
    }

    /**
     * Handle the FacilityMaintenance "updated" event.
     */
    public function updated(FacilityMaintenance $facilityMaintenance): void
    {
        // If the maintenance period has ended or is now in the past
        if ($facilityMaintenance->isDirty('end_date') && $facilityMaintenance->end_date->isPast()) {
            $this->checkAndRevertFacilityStatus($facilityMaintenance->facility);
        }
    }

    /**
     * Handle the FacilityMaintenance "deleted" event.
     */
    public function deleted(FacilityMaintenance $facilityMaintenance): void
    {
        $this->checkAndRevertFacilityStatus($facilityMaintenance->facility);
    }

    /**
     * Check if a facility should revert to 'Active' status.
     */
    protected function checkAndRevertFacilityStatus(Facility $facility): void
    {
        // Check for any other ongoing or future maintenance records for this facility
        $hasActiveMaintenance = FacilityMaintenance::where('facility_id', $facility->id)
            ->where('end_date', '>=', now()) // Future or ongoing maintenance
            ->exists();

        // Check if the facility is currently in 'Maintenance' status due to this maintenance
        // and if there are no other active maintenances.
        if (!$hasActiveMaintenance && $facility->status === 'Maintenance') {
            $facility->status = 'Active';
            $facility->save();
        }
    }

    /**
     * Handle the FacilityMaintenance "restored" event.
     */
    public function restored(FacilityMaintenance $facilityMaintenance): void
    {
        // If restored, ensure facility status is 'Maintenance'
        $facility = $facilityMaintenance->facility;
        if ($facility->status !== 'Emergency Closure') {
            $facility->status = 'Maintenance';
            $facility->save();
        }
    }

    /**
     * Handle the FacilityMaintenance "force deleted" event.
     */
    public function forceDeleted(FacilityMaintenance $facilityMaintenance): void
    {
        $this->checkAndRevertFacilityStatus($facilityMaintenance->facility);
    }
}
