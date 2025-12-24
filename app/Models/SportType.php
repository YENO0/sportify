<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SportType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sportType) {
            if (empty($sportType->slug)) {
                $sportType->slug = Str::slug($sportType->name);
            }
        });

        static::updating(function ($sportType) {
            if ($sportType->isDirty('name') && empty($sportType->slug)) {
                $sportType->slug = Str::slug($sportType->name);
            }
        });
    }

    /**
     * Get the equipment for this sport type.
     */
    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    /**
     * Scope to get only active sport types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

