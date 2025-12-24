<?php

namespace App\Patterns\Decorator;

use App\Models\Equipment;

/**
 * Decorator Pattern - Concrete Decorator for Low Stock Alert
 * Adds low stock monitoring and alert features to equipment
 */
class LowStockAlertDecorator extends BaseEquipmentDecorator
{
    protected bool $enableAlerts;
    protected ?string $alertEmail;

    public function __construct(Equipment $equipment, bool $enableAlerts = true, ?string $alertEmail = null)
    {
        parent::__construct($equipment);
        $this->enableAlerts = $enableAlerts;
        $this->alertEmail = $alertEmail;
        
        $this->features[] = [
            'type' => 'low_stock_alert',
            'name' => 'Low Stock Alert System',
            'value' => $enableAlerts 
                ? 'Enabled' . ($alertEmail ? " (Email: {$alertEmail})" : '')
                : 'Disabled',
            'expiry_date' => null, // Alerts don't expire
        ];
    }

    public function apply(): void
    {
        parent::apply();
        
        // Ensure minimum stock amount is set if not already
        if ($this->equipment->minimum_stock_amount == 0 && $this->equipment->quantity > 0) {
            // Set a default minimum based on quantity (20% of total)
            $this->equipment->update([
                'minimum_stock_amount' => max(1, (int)($this->equipment->quantity * 0.2))
            ]);
        }
    }

    /**
     * Check if equipment is currently low on stock
     */
    public function isLowStock(): bool
    {
        return $this->equipment->isLowStock();
    }

    /**
     * Get alert message if stock is low
     */
    public function getAlertMessage(): ?string
    {
        if (!$this->enableAlerts || !$this->isLowStock()) {
            return null;
        }
        
        $shortage = $this->equipment->minimum_stock_amount - $this->equipment->available_quantity;
        return "Low stock alert: {$this->equipment->name} has {$this->equipment->available_quantity} units available, " .
               "which is below the minimum of {$this->equipment->minimum_stock_amount}. " .
               "Shortage: {$shortage} units.";
    }
}

