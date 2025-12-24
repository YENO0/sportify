<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'transaction_type',
        'user_id',
        'quantity',
        'notes',
        'transaction_date',
        'expected_return_date',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'expected_return_date' => 'date',
    ];

    /**
     * Get the equipment for the transaction.
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Get the user for the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

