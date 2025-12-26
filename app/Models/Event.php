<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\States\Event\EventState;
use App\States\Event\DraftState;
use App\States\Event\PendingState;
use App\States\Event\ApprovedState;
use App\States\Event\RejectedState;
use App\States\Event\FullState;
use App\States\RegistrationStatus\RegistrationStatusState;
use App\States\RegistrationStatus\NotOpenState as RegistrationNotOpenState;
use App\States\RegistrationStatus\OpenState as RegistrationOpenState;
use App\States\RegistrationStatus\FullState as RegistrationFullState;
use App\States\RegistrationStatus\ClosedState as RegistrationClosedState;
use App\States\EventStatus\EventLifecycleState;
use App\States\EventStatus\UpcomingState;
use App\States\EventStatus\OngoingState;
use App\States\EventStatus\CompletedState;
use App\States\EventStatus\CancelledState;
use InvalidArgumentException;
use App\Models\EventJoined;
use App\Models\Payment;
use App\Models\EquipmentBorrowing;
use App\Models\Facility;
use App\Models\Booking;

class Event extends Model
{
    use HasFactory;

    // Table name (optional if follows Laravel convention)
    protected $table = 'events';

    // Primary key
    protected $primaryKey = 'eventID';

    // Auto-incrementing
    public $incrementing = true;

    // Primary key type
    protected $keyType = 'int';

    // Mass assignable attributes
    protected $fillable = [
        'event_name',
        'event_description',
        'event_poster',
        'event_start_date',
        'event_start_time',
        'event_end_date',
        'event_end_time',
        'registration_due_date',
        'max_capacity',
        'price',
        'facility_id',
        'committee_id',
        'status',
        'registration_status',
        'event_status',
        'approved_by',
        'approved_at',
        'rejection_remark',
    ];

    /**
     * Temporary default while Facility module is not implemented.
     */
    protected $attributes = [
        'facility_id' => null,
    ];

    // Casts
    protected $casts = [
        'event_start_date' => 'datetime',
        'event_end_date' => 'datetime',
        'registration_due_date' => 'datetime',
        'price' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Committee who created the event
    public function committee()
    {
        return $this->belongsTo(User::class, 'committee_id');
    }

    // Admin who approved the event
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Venue of the event
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    // Students registered for the event
    public function registrations()
    {
        return $this->hasMany(EventJoined::class, 'eventID');
    }

    /**
     * Relationship: Event has many EventJoined (alias for registrations for payment compatibility)
     */
    public function eventJoined()
    {
        return $this->registrations();
    }

    /**
     * Convenience: Access all payments for this event through EventJoined
     */
    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            EventJoined::class,
            'eventID',       // Foreign key on EventJoined table
            'eventJoinedID', // Foreign key on Payment table
            'eventID',       // Local key on Event table
            'eventJoinedID'  // Local key on EventJoined table
        );
    }

    /**
     * Equipment borrowings for this event
     */
    public function equipmentBorrowings()
    {
        return $this->hasMany(EquipmentBorrowing::class, 'event_id', 'eventID');
    }

    /**
     * Resolve the current state object based on status.
     */
    public function state(): EventState
    {
        return match ($this->status) {
            'draft' => new DraftState($this),
            'pending' => new PendingState($this),
            'approved' => new ApprovedState($this),
            'rejected' => new RejectedState($this),
            'full' => new FullState($this),
            default => throw new InvalidArgumentException("Unknown event status '{$this->status}'."),
        };
    }

    /**
     * Resolve the current registration status state object.
     */
    public function registrationState(): RegistrationStatusState
    {
        return match ($this->registration_status) {
            'NotOpen' => new RegistrationNotOpenState($this),
            'Open' => new RegistrationOpenState($this),
            'Full' => new RegistrationFullState($this),
            'Closed' => new RegistrationClosedState($this),
            default => throw new InvalidArgumentException("Unknown registration status '{$this->registration_status}'."),
        };
    }

    /**
     * Resolve the current event lifecycle state object.
     */
    public function eventLifecycleState(): EventLifecycleState
    {
        return match ($this->event_status) {
            'Upcoming' => new UpcomingState($this),
            'Ongoing' => new OngoingState($this),
            'Completed' => new CompletedState($this),
            'Cancelled' => new CancelledState($this),
            default => throw new InvalidArgumentException("Unknown event lifecycle status '{$this->event_status}'."),
        };
    }

    /**
     * Scope: Only active events (registration not passed)
     */
    public function scopeActive($query)
    {
        return $query->where('registration_due_date', '>=', now());
    }

    /**
     * Helper: Count of registered participants
     */
    public function registeredCount()
    {
        return $this->registrations()->where('status', 'registered')->count();
    }

    /**
     * Check if the event has ended based on end date and time
     */
    public function hasEnded(): bool
    {
        // If no end date is set, use start date + start time as fallback
        if (!$this->event_end_date) {
            if (!$this->event_start_date) {
                return false; // Can't determine if event has ended without dates
            }
            
            // If no end date, check if start date/time has passed
            $startDateTime = $this->event_start_date;
            if ($this->event_start_time) {
                $startDateTime = $this->event_start_date->copy()->setTimeFromTimeString($this->event_start_time);
            }
            
            // Default to 3 hours duration if no end time specified
            $endDateTime = $startDateTime->copy()->addHours(3);
            return $endDateTime->isPast();
        }

        // Combine end date with end time if available
        $endDateTime = $this->event_end_date;
        if ($this->event_end_time) {
            $endDateTime = $this->event_end_date->copy()->setTimeFromTimeString($this->event_end_time);
        }

        return $endDateTime->isPast();
    }

    /**
     * Unbook the facility associated with this event.
     * Finds and deletes bookings that match this event's facility, committee, and time slot.
     * 
     * @return int Number of bookings deleted
     */
    public function unbookFacility(): int
    {
        // Only unbook if event has a facility and committee
        if (!$this->facility_id || !$this->committee_id) {
            return 0;
        }

        // Calculate booking start and end datetime (matching how bookings are created in EventController)
        if (!$this->event_start_date) {
            return 0; // Can't determine booking time without start date
        }

        // Build start datetime (same logic as EventController::store)
        $bookingStart = \Carbon\Carbon::parse($this->event_start_date->format('Y-m-d') . ' ' . ($this->event_start_time ?? '00:00:00'));

        // Build end datetime (same logic as EventController::store)
        if ($this->event_end_date) {
            $bookingEnd = \Carbon\Carbon::parse($this->event_end_date->format('Y-m-d') . ' ' . ($this->event_end_time ?? '23:59:59'));
        } else {
            // Single event: use start date with end time
            $bookingEnd = \Carbon\Carbon::parse($this->event_start_date->format('Y-m-d') . ' ' . ($this->event_end_time ?? '23:59:59'));
        }

        // Find bookings that match:
        // - Same facility
        // - Same committee (user_id)
        // - Same or overlapping time slot (with small tolerance for exact matches)
        // Use overlapping query to handle any minor time differences
        $bookings = Booking::where('facility_id', $this->facility_id)
            ->where('user_id', $this->committee_id)
            ->where(function ($query) use ($bookingStart, $bookingEnd) {
                // Match bookings that overlap with the event time slot
                // This ensures we catch the booking even if there are minor time differences
                $query->where(function ($q) use ($bookingStart, $bookingEnd) {
                    $q->where('start_time', '<=', $bookingEnd)
                      ->where('end_time', '>=', $bookingStart);
                });
            })
            ->get();

        $deletedCount = 0;
        foreach ($bookings as $booking) {
            $booking->delete();
            $deletedCount++;
        }

        return $deletedCount;
    }
}
