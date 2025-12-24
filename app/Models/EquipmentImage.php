<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EquipmentImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'display_order',
        'alt_text',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Get the equipment that owns the image.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the full URL for the image.
     */
    public function getUrlAttribute(): string
    {
        // Use asset() to generate URL relative to current request
        // This ensures it works regardless of APP_URL configuration
        return asset('storage/' . $this->file_path);
    }
}

