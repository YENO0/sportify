<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EquipmentBorrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'equipment_id',
        'quantity',
        'status',
        'borrowed_at',
        'returned_at',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /**
     * Get the event that this borrowing belongs to.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the equipment that was borrowed.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user who created the borrowing.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the borrowing has been returned.
     */
    public function isReturned(): bool
    {
        return $this->status === 'returned' && $this->returned_at !== null;
    }

    /**
     * Check if the borrowing should be returned based on event end time.
     */
    public function shouldBeReturned(): bool
    {
        if ($this->isReturned()) {
            return false;
        }

        return $this->event->hasEnded();
    }
}

