<?php

namespace App\Patterns\Factory;

use App\Models\Equipment;

/**
 * Factory Method Pattern - Concrete Creator for Outdoor Equipment
 */
class OutdoorEquipmentFactory extends AbstractEquipmentFactory
{
    protected function setDefaultValues(array $data): array
    {
        $data['type'] = 'outdoor';
        $data['status'] = $data['status'] ?? 'available';
        $data['available_quantity'] = $data['available_quantity'] ?? $data['quantity'];
        $data['location'] = $data['location'] ?? 'Outdoor Storage';
        
        // Factory Method: Set default minimum stock for outdoor equipment (typically 4 units)
        $data['minimum_stock_amount'] = $data['minimum_stock_amount'] ?? ($data['quantity'] > 0 ? max(4, (int)($data['quantity'] * 0.18)) : 4);
        
        return $data;
    }

    protected function makeEquipment(array $data): Equipment
    {
        return Equipment::create($data);
    }
}

