<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventJoined extends Model
{
    protected $table = 'eventJoined';
    protected $primaryKey = 'eventJoinedID';

    protected $fillable = [
        'eventID',
        'studentID',
        'paymentID',
        'status',
        'joinedDate',
    ];
}

