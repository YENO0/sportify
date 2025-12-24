<?php

namespace App\Patterns\Decorator;

use App\Models\Equipment;

/**
 * Decorator Pattern - Concrete Decorator for Maintenance Tracking
 */
class MaintenanceTrackingDecorator extends BaseEquipmentDecorator
{
    public function __construct(Equipment $equipment, int $maintenanceIntervalMonths = 3)
    {
        parent::__construct($equipment);
        
        $this->features[] = [
            'type' => 'maintenance_tracking',
            'name' => 'Automated Maintenance Tracking',
            'value' => "Interval: {$maintenanceIntervalMonths} months",
            'expiry_date' => null, // Maintenance tracking doesn't expire
        ];
        
        // Set next maintenance date
        $this->equipment->next_maintenance_date = now()->addMonths($maintenanceIntervalMonths);
    }
}

