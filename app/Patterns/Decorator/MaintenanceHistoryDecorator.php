<?php

namespace App\Patterns\Decorator;

use App\Models\Equipment;

/**
 * Decorator Pattern - Concrete Decorator for Maintenance History Tracking
 */
class MaintenanceHistoryDecorator extends BaseEquipmentDecorator
{
    public function __construct(Equipment $equipment, bool $enableHistoryTracking = true)
    {
        parent::__construct($equipment);
        
        $this->features[] = [
            'type' => 'maintenance_history',
            'name' => 'Maintenance History Tracking',
            'value' => $enableHistoryTracking ? 'Enabled' : 'Disabled',
            'expiry_date' => null, // History tracking doesn't expire
        ];
    }

    public function apply(): void
    {
        parent::apply();
        
        // Ensure equipment has maintenance tracking enabled
        if ($this->equipment->next_maintenance_date === null) {
            $this->equipment->update([
                'next_maintenance_date' => now()->addMonths(3)
            ]);
        }
    }
}

