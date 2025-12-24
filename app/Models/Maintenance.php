<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'equipment_id',
        'maintenance_type',
        'title',
        'description',
        'scheduled_date',
        'start_date',
        'end_date',
        'quantity',
        'completed_date',
        'status',
        'cost',
        'assigned_to',
        'notes',
        'technician_notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'completed_date' => 'date',
        'cost' => 'decimal:2',
    ];

    /**
     * Get the equipment for the maintenance.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user assigned to the maintenance.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Check if maintenance is overdue.
     */
    public function isOverdue(): bool
    {
        $dateToCheck = $this->start_date ?? $this->scheduled_date;
        return $this->status === 'pending' && $dateToCheck && $dateToCheck->isPast();
    }

    /**
     * Check if maintenance has ended and should return quantity.
     */
    public function shouldReturnQuantity(): bool
    {
        return $this->status === 'completed' 
            || ($this->end_date && $this->end_date->isPast() && $this->status !== 'cancelled');
    }

    /**
     * Check if maintenance is active (between start and end date).
     */
    public function isActive(): bool
    {
        if (!$this->start_date || !$this->end_date) {
            return false;
        }
        $today = now()->toDateString();
        return $today >= $this->start_date->toDateString() 
            && $today <= $this->end_date->toDateString()
            && in_array($this->status, ['pending', 'in_progress']);
    }

    /**
     * Check if maintenance is upcoming (within next 7 days).
     */
    public function isUpcoming(): bool
    {
        return $this->status === 'pending' 
            && $this->scheduled_date->isFuture() 
            && $this->scheduled_date->diffInDays(now()) <= 7;
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => $this->isOverdue() ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

