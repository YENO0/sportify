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
    public function facility(): ?BelongsTo
    {
        // Avoid errors until a Facility model/table exists
        if (!class_exists('App\\Models\\Facility')) {
            return null;
        }

        return $this->belongsTo('App\\Models\\Facility', 'facility_id');
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
}
