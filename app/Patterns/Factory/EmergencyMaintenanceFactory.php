<?php

namespace App\Patterns\Factory;

use App\Models\Maintenance;

/**
 * Factory Method Pattern - Concrete Creator for Emergency Maintenance
 */
class EmergencyMaintenanceFactory extends AbstractMaintenanceFactory
{
    protected function setDefaultValues(array $data): array
    {
        $data['maintenance_type'] = 'emergency';
        $data['status'] = $data['status'] ?? 'pending';
        $data['quantity'] = $data['quantity'] ?? 1;
        
        // Emergency maintenance is scheduled for today or immediate
        if (empty($data['scheduled_date'])) {
            $data['scheduled_date'] = now()->toDateString();
        }
        
        // Set start_date to today for emergency
        if (empty($data['start_date'])) {
            $data['start_date'] = now()->toDateString();
        }
        
        // Emergency maintenance typically has urgent description
        if (empty($data['description'])) {
            $data['description'] = 'Emergency maintenance required';
        }
        
        return $data;
    }

    protected function makeMaintenance(array $data): Maintenance
    {
        return Maintenance::create($data);
    }

    protected function postCreation(Maintenance $maintenance, array $data): void
    {
        // Factory Method: Emergency maintenance immediately deducts quantity
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
                "Insufficient quantity for emergency maintenance. Available: {$equipment->available_quantity}, Required: {$maintenance->quantity}"
            );
        }
    }
}

