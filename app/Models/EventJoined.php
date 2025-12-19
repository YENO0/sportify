<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventJoined extends Model
{
    use HasFactory;

    protected $table = 'eventJoined';
    protected $primaryKey = 'eventJoinedID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'eventID',
        'studentID',
        'status',
        'joinedDate',
    ];

    protected $casts = [
        'joinedDate' => 'datetime',
    ];

    /**
     * EventJoined belongs to Event
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'eventID', 'eventID');
    }

    /**
     * EventJoined has one Payment
     */
    public function payment()
    {
        return $this->hasOne(Payment::class, 'eventJoinedID', 'eventJoinedID');
    }

    /**
     * EventJoined has one Invoice
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'eventJoinedID', 'eventJoinedID');
    }

    /**
     * Scope: registered only
     */
    public function scopeRegistered($query)
    {
        return $query->where('status', 'registered');
    }
}
