<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'title',
        'description',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}