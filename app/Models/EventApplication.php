<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventApplication extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'committee_member_id',
        'event_name',
        'description',
        'event_date',
        'proposed_budget',
        'status',
        'admin_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'event_date' => 'date',
    ];

    /**
     * Get the committee member that owns the event application.
     */
    public function committeeMember(): BelongsTo
    {
        return $this->belongsTo(User::class, 'committee_member_id');
    }
}
