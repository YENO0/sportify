<?php

namespace App\Patterns\Factory;

use App\Models\Maintenance;

/**
 * Factory Method Pattern - Concrete Creator for Scheduled Maintenance
 */
class ScheduledMaintenanceFactory extends AbstractMaintenanceFactory
{
    protected function setDefaultValues(array $data): array
    {
        $data['maintenance_type'] = 'scheduled';
        $data['status'] = $data['status'] ?? 'pending';
        $data['quantity'] = $data['quantity'] ?? 1;
        
        // Set start_date from scheduled_date if not provided
        if (empty($data['start_date']) && !empty($data['scheduled_date'])) {
            $data['start_date'] = $data['scheduled_date'];
        }
        
        // Scheduled maintenance typically has a description
        if (empty($data['description'])) {
            $data['description'] = 'Regular scheduled maintenance';
        }
        
        return $data;
    }

    protected function makeMaintenance(array $data): Maintenance
    {
        return Maintenance::create($data);
    }

    protected function postCreation(Maintenance $maintenance, array $data): void
    {
        // Factory Method: Deduct quantity from equipment when scheduled maintenance starts
        // Only deduct if start_date is today or in the past
        if ($maintenance->equipment && $maintenance->start_date && $maintenance->start_date->lte(now())) {
            $this->deductQuantity($maintenance);
        }
        
        // Update equipment's next maintenance date if this is scheduled
        if ($maintenance->equipment && isset($data['update_next_maintenance'])) {
            $maintenance->equipment->update([
                'next_maintenance_date' => ($maintenance->end_date ?? $maintenance->scheduled_date)->copy()->addMonths(3)
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

