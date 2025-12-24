<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'location',
        'status',
        'user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the user that created the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all equipment borrowings for this event.
     */
    public function equipmentBorrowings()
    {
        return $this->hasMany(EquipmentBorrowing::class);
    }

    /**
     * Get the start datetime.
     */
    public function getStartDateTimeAttribute()
    {
        $time = is_string($this->start_time) ? $this->start_time : $this->start_time;
        return Carbon::parse($this->start_date->format('Y-m-d') . ' ' . $time);
    }

    /**
     * Get the end datetime.
     */
    public function getEndDateTimeAttribute()
    {
        $time = is_string($this->end_time) ? $this->end_time : $this->end_time;
        return Carbon::parse($this->end_date->format('Y-m-d') . ' ' . $time);
    }

    /**
     * Check if the event has ended.
     */
    public function hasEnded(): bool
    {
        return Carbon::now()->greaterThan($this->end_datetime);
    }

    /**
     * Check if the event is currently ongoing.
     */
    public function isOngoing(): bool
    {
        $now = Carbon::now();
        return $now->greaterThanOrEqualTo($this->start_datetime) && $now->lessThanOrEqualTo($this->end_datetime);
    }

    /**
     * Check if the event is upcoming.
     */
    public function isUpcoming(): bool
    {
        return Carbon::now()->lessThan($this->start_datetime);
    }
}

