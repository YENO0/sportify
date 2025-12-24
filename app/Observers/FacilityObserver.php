<?php

namespace App\Observers;

use App\Models\Facility;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\FacilityClosureNotification;
use App\Notifications\SystemFacilityStatusNotification;
use Illuminate\Support\Facades\Notification;

class FacilityObserver
{
    /**
     * Handle the Facility "updated" event.
     */
    public function updated(Facility $facility): void
    {
        if ($facility->isDirty('status') && in_array($facility->status, ['Emergency Closure', 'Maintenance'])) {
            $closureReason = $facility->status; // 'Emergency Closure' or 'Maintenance'

            $bookingsQuery = Booking::where('facility_id', $facility->id)
                ->where('start_time', '>', now())
                ->where('status', 'approved');

            if ($facility->status === 'Maintenance') {
                // If maintenance, only cancel bookings overlapping with the maintenance period
                // We find the maintenance record that caused this status change.
                // Assuming the status change was triggered by the maintenance observer or command
                // which means there should be an active maintenance record covering NOW.
                $maintenance = \App\Models\FacilityMaintenance::where('facility_id', $facility->id)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>', now())
                    ->first();

                if ($maintenance) {
                    $bookingsQuery->where(function ($query) use ($maintenance) {
                        $query->where(function ($q) use ($maintenance) {
                            $q->where('start_time', '<', $maintenance->end_date)
                              ->where('end_time', '>', $maintenance->start_date);
                        });
                    });
                } else {
                     // Fallback: if no specific maintenance record found (manual status change?), 
                     // cancel all future bookings? Or maybe none? 
                     // Let's stick to cancelling all future bookings if no specific maintenance range is found to be safe,
                     // effectively behaving like manual maintenance mode.
                }
            }
            
            $bookings = $bookingsQuery->get();

            foreach ($bookings as $booking) {
                $user = $booking->user;
                Notification::send($user, new FacilityClosureNotification($facility, $booking, $closureReason));
                $booking->update(['status' => 'cancelled']);
            }

            // Send system-wide notification to all users
            $users = \App\Models\User::all(); // Get all users
            foreach ($users as $user) {
                Notification::send($user, new SystemFacilityStatusNotification($facility, $closureReason));
            }
        }
    }
}
