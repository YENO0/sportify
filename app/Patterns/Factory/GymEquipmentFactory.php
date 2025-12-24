<?php

namespace App\Patterns\Factory;

use App\Models\Equipment;

/**
 * Factory Method Pattern - Concrete Creator for Gym Equipment
 */
class GymEquipmentFactory extends AbstractEquipmentFactory
{
    protected function setDefaultValues(array $data): array
    {
        $data['type'] = 'gym';
        $data['status'] = $data['status'] ?? 'available';
        $data['available_quantity'] = $data['available_quantity'] ?? $data['quantity'];
        $data['location'] = $data['location'] ?? 'Gym Floor';
        
        // Gym equipment typically requires maintenance tracking
        if (!isset($data['next_maintenance_date'])) {
            $data['next_maintenance_date'] = now()->addMonths(3);
        }
        
        // Factory Method: Set default minimum stock for gym equipment (typically 3 units, as they're larger items)
        $data['minimum_stock_amount'] = $data['minimum_stock_amount'] ?? ($data['quantity'] > 0 ? max(3, (int)($data['quantity'] * 0.15)) : 3);
        
        return $data;
    }

    protected function makeEquipment(array $data): Equipment
    {
        return Equipment::create($data);
    }

    protected function postCreation(Equipment $equipment, array $data): void
    {
        // Gym equipment might need initial maintenance record
        if (isset($data['initial_maintenance']) && $data['initial_maintenance']) {
            $equipment->update(['last_maintenance_date' => now()]);
        }
    }
}

