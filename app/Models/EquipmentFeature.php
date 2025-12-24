<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'feature_type',
        'feature_name',
        'feature_value',
        'expiry_date',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    /**
     * Get the equipment that owns the feature.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Check if feature is expired.
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date->isPast();
    }
}

