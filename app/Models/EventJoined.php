<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventJoined extends Model
{
    use HasFactory;

    // Table name (optional if follows Laravel convention)
    protected $table = 'event_joineds';

    // Primary key
    protected $primaryKey = 'eventJoinedID';

    // Auto-incrementing
    public $incrementing = true;

    // Primary key type
    protected $keyType = 'int';

    // Mass assignable attributes
    protected $fillable = [
        'eventID',
        'studentID',
        'paymentID',
        'status',
        'joinedDate',
    ];

    // Casts
    protected $casts = [
        'joinedDate' => 'datetime',
    ];

    /**
     * Relationship: EventJoined belongs to Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'eventID', 'eventID');
    }

    /**
     * Relationship: EventJoined has one Payment
     */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'eventJoinedID', 'eventJoinedID');
    }

    /**
     * (Optional) Convenience: scope for registered students only
     */
    public function scopeRegistered($query)
    {
        return $query->where('status', 'registered');
    }
}
