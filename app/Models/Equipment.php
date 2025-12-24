<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'brand_id',
        'model',
        'description',
        'quantity',
        'available_quantity',
        'minimum_stock_amount',
        'price',
        'status',
        'location',
        'purchase_date',
        'last_maintenance_date',
        'next_maintenance_date',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'price' => 'decimal:2',
    ];

    /**
     * Get the features for the equipment.
     */
    public function features()
    {
        return $this->hasMany(EquipmentFeature::class);
    }

    /**
     * Get the transactions for the equipment.
     */
    public function transactions()
    {
        return $this->hasMany(EquipmentTransaction::class);
    }

    /**
     * Get the brand for the equipment.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the maintenances for the equipment.
     */
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    /**
     * Get upcoming maintenances.
     */
    public function upcomingMaintenances()
    {
        return $this->maintenances()
            ->where('status', 'pending')
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date', 'asc');
    }

    /**
     * Get overdue maintenances.
     */
    public function overdueMaintenances()
    {
        return $this->maintenances()
            ->where('status', 'pending')
            ->where('scheduled_date', '<', now());
    }

    /**
     * Check if equipment is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->available_quantity > 0;
    }

    /**
     * Get utilization percentage.
     */
    public function getUtilizationPercentage(): float
    {
        if ($this->quantity == 0) {
            return 0;
        }
        $used = $this->quantity - $this->available_quantity;
        return ($used / $this->quantity) * 100;
    }

    /**
     * Check if stock is below minimum.
     */
    public function isLowStock(): bool
    {
        return $this->available_quantity < $this->minimum_stock_amount;
    }

    /**
     * Get stock level status.
     */
    public function getStockLevelStatus(): string
    {
        if ($this->available_quantity == 0) {
            return 'out_of_stock';
        }
        if ($this->isLowStock()) {
            return 'low_stock';
        }
        return 'adequate';
    }

    /**
     * Get stock level percentage relative to minimum.
     */
    public function getStockLevelPercentage(): float
    {
        if ($this->minimum_stock_amount == 0) {
            return 100;
        }
        return min(100, ($this->available_quantity / $this->minimum_stock_amount) * 100);
    }
}

