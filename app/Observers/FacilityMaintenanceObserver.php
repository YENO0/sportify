<?php

namespace App\Observers;

use App\Models\FacilityMaintenance;
use App\Models\Facility;
use App\Models\User;
use App\Models\Event;
use App\Notifications\FacilityMaintenanceNotification;
use App\Notifications\EventCancelledDueToMaintenanceNotification;
use App\Notifications\CommitteeEventCancelledNotification;
use App\Services\EventStatusService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

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

        // Check for conflicting approved events and cancel them
        $this->checkAndCancelConflictingEvents($facilityMaintenance);

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
        // Check for conflicting events if dates changed
        if ($facilityMaintenance->isDirty(['start_date', 'end_date', 'facility_id'])) {
            $this->checkAndCancelConflictingEvents($facilityMaintenance);
        }

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

    /**
     * Check for approved events that conflict with maintenance dates and cancel them.
     */
    protected function checkAndCancelConflictingEvents(FacilityMaintenance $maintenance): void
    {
        $facility = $maintenance->facility;
        
        if (!$facility) {
            Log::warning("FacilityMaintenanceObserver: Facility not found for maintenance", [
                'maintenance_id' => $maintenance->id,
            ]);
            return;
        }

        // Get maintenance dates (already datetime from casts)
        $maintenanceStart = $maintenance->start_date;
        $maintenanceEnd = $maintenance->end_date;

        Log::info("FacilityMaintenanceObserver: Checking for conflicting events", [
            'facility_id' => $facility->id,
            'facility_name' => $facility->name,
            'maintenance_id' => $maintenance->id,
            'maintenance_start' => $maintenanceStart,
            'maintenance_end' => $maintenanceEnd,
        ]);

        // First, get all approved events for this facility that are not already cancelled
        $allApprovedEvents = Event::where('facility_id', $facility->id)
            ->where('status', 'approved')
            ->where(function ($query) {
                $query->where('event_status', '!=', 'Cancelled')
                      ->orWhereNull('event_status');
            })
            ->get();

        Log::info("FacilityMaintenanceObserver: All approved events for facility", [
            'facility_id' => $facility->id,
            'total_approved_events' => $allApprovedEvents->count(),
            'events' => $allApprovedEvents->map(function ($event) {
                return [
                    'event_id' => $event->eventID,
                    'event_name' => $event->event_name,
                    'event_start_date' => $event->event_start_date ? $event->event_start_date->format('Y-m-d H:i:s') : null,
                    'event_end_date' => $event->event_end_date ? $event->event_end_date->format('Y-m-d H:i:s') : null,
                    'event_status' => $event->event_status,
                ];
            })->toArray(),
        ]);

        // Filter events that overlap with maintenance period
        // Two date ranges overlap if: start1 < end2 AND start2 < end1
        $conflictingEvents = $allApprovedEvents->filter(function ($event) use ($maintenanceStart, $maintenanceEnd) {
            $eventStart = $event->event_start_date;
            $eventEnd = $event->event_end_date ?? $event->event_start_date; // If no end_date, treat as single day event
            
            // Check if dates overlap
            // Event overlaps maintenance if: event_start < maintenance_end AND maintenance_start < event_end
            $overlaps = $eventStart < $maintenanceEnd && $maintenanceStart < $eventEnd;
            
            Log::info("FacilityMaintenanceObserver: Checking event overlap", [
                'event_id' => $event->eventID,
                'event_name' => $event->event_name,
                'event_start' => $eventStart ? $eventStart->format('Y-m-d H:i:s') : null,
                'event_end' => $eventEnd ? $eventEnd->format('Y-m-d H:i:s') : null,
                'maintenance_start' => $maintenanceStart->format('Y-m-d H:i:s'),
                'maintenance_end' => $maintenanceEnd->format('Y-m-d H:i:s'),
                'overlaps' => $overlaps,
            ]);
            
            return $overlaps;
        });

        Log::info("FacilityMaintenanceObserver: Found conflicting events", [
            'count' => $conflictingEvents->count(),
            'events' => $conflictingEvents->map(function ($event) {
                return [
                    'event_id' => $event->eventID,
                    'event_name' => $event->event_name,
                    'event_start_date' => $event->event_start_date,
                    'event_end_date' => $event->event_end_date,
                    'event_status' => $event->event_status,
                ];
            })->toArray(),
        ]);

        foreach ($conflictingEvents as $event) {
            try {
                Log::info("FacilityMaintenanceObserver: Cancelling event", [
                    'event_id' => $event->eventID,
                    'event_name' => $event->event_name,
                    'current_event_status' => $event->event_status,
                ]);

                // Cancel the event by setting event_status to Cancelled
                $event->event_status = 'Cancelled';
                $event->save();
                
                // Refresh to get updated status
                $event->refresh();
                
                Log::info("FacilityMaintenanceObserver: Event status updated", [
                    'event_id' => $event->eventID,
                    'new_event_status' => $event->event_status,
                ]);
                
                // Sync event status (this might override our change, so we do it after)
                EventStatusService::sync($event);
                
                // Ensure it's still cancelled after sync
                $event->refresh();
                if ($event->event_status !== 'Cancelled') {
                    $event->event_status = 'Cancelled';
                    $event->save();
                }

                // Unbook the facility when event is cancelled due to maintenance
                $event->unbookFacility();

                // Update facility status if no upcoming/ongoing events remain
                if ($event->facility_id) {
                    $facility = \App\Models\Facility::find($event->facility_id);
                    if ($facility) {
                        $facility->updateStatusBasedOnEvents();
                    }
                }

                // Notify the committee member who created the event
                if ($event->committee) {
                    Notification::send(
                        $event->committee,
                        new CommitteeEventCancelledNotification($event, $facility, $maintenance)
                    );
                }

                // Notify all registered students
                $registrations = $event->registrations()
                    ->whereIn('status', ['registered', 'waitlisted'])
                    ->with('user')
                    ->get();

                foreach ($registrations as $registration) {
                    if ($registration->user) {
                        Notification::send(
                            $registration->user,
                            new EventCancelledDueToMaintenanceNotification($event, $facility, $maintenance)
                        );
                    }
                }

                Log::info("Event cancelled due to maintenance conflict", [
                    'event_id' => $event->eventID,
                    'event_name' => $event->event_name,
                    'facility_id' => $facility->id,
                    'facility_name' => $facility->name,
                    'maintenance_id' => $maintenance->id,
                    'registrations_notified' => $registrations->count(),
                    'final_event_status' => $event->event_status,
                ]);

            } catch (\Exception $e) {
                Log::error("Failed to cancel event due to maintenance conflict", [
                    'event_id' => $event->eventID,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }
}
