<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'image',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all events for this facility
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'facility_id', 'id');
    }

    /**
     * Check if facility has any upcoming or ongoing events
     * 
     * @return bool
     */
    public function hasUpcomingOrOngoingEvents(): bool
    {
        $now = \Carbon\Carbon::now();

        return $this->events()
            ->where('status', 'approved')
            ->where('event_status', '!=', 'Cancelled')
            ->whereIn('event_status', ['Upcoming', 'Ongoing'])
            ->where(function ($query) use ($now) {
                // Events that haven't ended yet
                $query->whereNull('event_end_date')
                      ->orWhere(function ($q) use ($now) {
                          // End date is in the future
                          $q->where('event_end_date', '>', $now->toDateString())
                            ->orWhere(function ($subQ) use ($now) {
                                // End date is today but end time is in the future
                                $subQ->whereDate('event_end_date', '=', $now->toDateString())
                                     ->where(function ($sq) use ($now) {
                                         $sq->whereNull('event_end_time')
                                            ->orWhereTime('event_end_time', '>', $now->toTimeString());
                                     });
                            });
                      });
            })
            ->exists();
    }

    /**
     * Update facility status based on events and maintenance
     * Sets status to 'Active' if no upcoming/ongoing events and no active maintenance
     */
    public function updateStatusBasedOnEvents(): void
    {
        // Don't change status if facility is in Maintenance or Emergency Closure
        if (in_array($this->status, ['Maintenance', 'Emergency Closure'])) {
            return;
        }

        // Check for active maintenance
        $hasActiveMaintenance = \App\Models\FacilityMaintenance::where('facility_id', $this->id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>', now())
            ->exists();

        // If there's active maintenance, status should be 'Maintenance' (handled by maintenance observer)
        if ($hasActiveMaintenance) {
            return;
        }

        // Check for upcoming or ongoing events
        $hasEvents = $this->hasUpcomingOrOngoingEvents();

        // If no upcoming/ongoing events and no maintenance, set to Active
        if (!$hasEvents && $this->status !== 'Active') {
            $this->status = 'Active';
            $this->save();
        }
    }
}
