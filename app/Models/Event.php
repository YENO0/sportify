<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'event_end_date',
        'registration_due_date',
        'max_capacity',
        'facility_id',
        'price',
        'committee_id',
        'approved_by',
        'status',
        'approved_at',
        'rejection_remark',
    ];

    // Casts
    protected $casts = [
        'event_start_date' => 'datetime',
        'event_end_date' => 'datetime',
        'registration_due_date' => 'datetime',
        'price' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Relationship: Event has many EventJoined
     */
    public function eventJoineds()
    {
        return $this->hasMany(EventJoined::class, 'eventID', 'eventID');
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
        return $this->eventJoineds()->where('status', 'registered')->count();
    }
}
