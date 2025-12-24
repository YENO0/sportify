<?php

namespace App\Patterns\Factory;

use App\Models\Maintenance;

/**
 * Factory Method Pattern - Concrete Creator for Preventive Maintenance
 */
class PreventiveMaintenanceFactory extends AbstractMaintenanceFactory
{
    protected function setDefaultValues(array $data): array
    {
        $data['maintenance_type'] = 'preventive';
        $data['status'] = $data['status'] ?? 'pending';
        $data['quantity'] = $data['quantity'] ?? 1;
        
        // Set start_date from scheduled_date if not provided
        if (empty($data['start_date']) && !empty($data['scheduled_date'])) {
            $data['start_date'] = $data['scheduled_date'];
        }
        
        // Preventive maintenance description
        if (empty($data['description'])) {
            $data['description'] = 'Preventive maintenance to avoid future issues';
        }
        
        return $data;
    }

    protected function makeMaintenance(array $data): Maintenance
    {
        return Maintenance::create($data);
    }

    protected function postCreation(Maintenance $maintenance, array $data): void
    {
        // Factory Method: Deduct quantity when preventive maintenance starts
        // Only deduct if start_date is today or in the past
        if ($maintenance->equipment && $maintenance->start_date && $maintenance->start_date->lte(now())) {
            $this->deductQuantity($maintenance);
        }
        
        // Preventive maintenance might update last maintenance date
        if ($maintenance->equipment && isset($data['update_last_maintenance'])) {
            $maintenance->equipment->update([
                'last_maintenance_date' => now()
            ]);
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
                "Insufficient quantity. Available: {$equipment->available_quantity}, Required: {$maintenance->quantity}"
            );
        }
    }
}

