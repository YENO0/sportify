<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Table name (optional if follows Laravel convention)
    protected $table = 'payments';

    // Primary key
    protected $primaryKey = 'paymentID';

    // Auto-incrementing
    public $incrementing = true;

    // Data type of primary key
    protected $keyType = 'int';

    // Mass assignable attributes
    protected $fillable = [
        'paymentMethod',
        'paymentDate',
        'paymentAmount',
        'eventJoinedID'
    ];

    // Casts
    protected $casts = [
        'paymentDate' => 'datetime',
        'paymentAmount' => 'decimal:2',
    ];

    /**
     * Relationship: Payment belongs to an EventJoined
     */
    public function eventJoined()
    {
        return $this->belongsTo(EventJoined::class, 'eventJoinedID', 'eventJoinedID');
    }

    /**
     * Relationship: Access the User via EventJoined
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            EventJoined::class,
            'eventJoinedID', // Foreign key on EventJoined table
            'id',            // Foreign key on User table (users.id)
            'eventJoinedID', // Local key on Payment
            'studentID'      // Local key on EventJoined (studentID references users.id)
        );
    }

    /**
     * Convenience relationship: Access the Event via EventJoined
     */
    public function event()
    {
        return $this->hasOneThrough(
            Event::class,
            EventJoined::class,
            'eventJoinedID', // Foreign key on EventJoined table
            'eventID',       // Foreign key on Event table
            'eventJoinedID', // Local key on Payment
            'eventID'        // Local key on EventJoined
        );
    }
}