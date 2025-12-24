<?php

namespace App\Patterns\Factory;

use App\Models\Maintenance;

/**
 * Factory Method Pattern - Concrete Creator for Repair Maintenance
 */
class RepairMaintenanceFactory extends AbstractMaintenanceFactory
{
    protected function setDefaultValues(array $data): array
    {
        $data['maintenance_type'] = 'repair';
        $data['status'] = $data['status'] ?? 'pending';
        $data['quantity'] = $data['quantity'] ?? 1;
        
        // Set start_date from scheduled_date if not provided
        if (empty($data['start_date']) && !empty($data['scheduled_date'])) {
            $data['start_date'] = $data['scheduled_date'];
        }
        
        // Repair maintenance description
        if (empty($data['description'])) {
            $data['description'] = 'Equipment repair and restoration';
        }
        
        return $data;
    }

    protected function makeMaintenance(array $data): Maintenance
    {
        return Maintenance::create($data);
    }

    protected function postCreation(Maintenance $maintenance, array $data): void
    {
        // Factory Method: Repair maintenance immediately deducts quantity
        if ($maintenance->equipment) {
            $this->deductQuantity($maintenance);
            $maintenance->equipment->update(['status' => 'maintenance']);
        }
    }

    /**
     * Deduct quantity from equipment (Factory Method specific logic)
     */
    protected function deductQuantity(Maintenance $maintenance): void
    {
        $equipment = $maintenance->equipment;
        if ($equipment->available_quantity >= $maintenance->quantity) {
            $equipment->available_quantity -= $maintenance->quantity;
            $equipment->save();
        } else {
            throw new \InvalidArgumentException(
                "Insufficient quantity for repair. Available: {$equipment->available_quantity}, Required: {$maintenance->quantity}"
            );
        }
    }
}

