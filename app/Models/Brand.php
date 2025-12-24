<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'website',
        'contact_email',
    ];

    /**
     * Get the equipment for the brand.
     */
    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    /**
     * Get the count of equipment for this brand.
     */
    public function getEquipmentCountAttribute(): int
    {
        return $this->equipment()->count();
    }
}

