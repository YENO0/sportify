<?php

namespace App\Patterns\Decorator;

use App\Models\Maintenance;

/**
 * Decorator Pattern - Concrete Decorator for Maintenance Quantity Management
 * Adds quantity management features to maintenance
 */
class MaintenanceQuantityDecorator
{
    protected Maintenance $maintenance;
    protected bool $quantityDeducted = false;

    public function __construct(Maintenance $maintenance)
    {
        $this->maintenance = $maintenance;
        $this->checkIfQuantityDeducted();
    }

    /**
     * Check if quantity was already deducted
     */
    protected function checkIfQuantityDeducted(): void
    {
        if (!$this->maintenance->equipment) {
            return;
        }

        // If available quantity is less than (total - maintenance quantity), 
        // it means quantity was likely already deducted
        $expectedMinAvailable = $this->maintenance->equipment->quantity - $this->maintenance->quantity;
        $this->quantityDeducted = $this->maintenance->equipment->available_quantity <= $expectedMinAvailable;
    }

    /**
     * Get the maintenance
     */
    public function getMaintenance(): Maintenance
    {
        return $this->maintenance;
    }

    /**
     * Deduct quantity from equipment when maintenance starts
     */
    public function deductQuantityOnStart(): void
    {
        if (!$this->maintenance->equipment) {
            return;
        }

        if ($this->quantityDeducted) {
            return; // Already deducted
        }

        $equipment = $this->maintenance->equipment;
        
        // Deduct if start date is today or past, or if status is in_progress
        $shouldDeduct = false;
        
        if ($this->maintenance->start_date && $this->maintenance->start_date->lte(now())) {
            $shouldDeduct = true;
        }
        
        if ($this->maintenance->status === 'in_progress') {
            $shouldDeduct = true;
        }
        
        if ($shouldDeduct) {
            if ($equipment->available_quantity < $this->maintenance->quantity) {
                throw new \InvalidArgumentException(
                    "Insufficient quantity. Available: {$equipment->available_quantity}, Required: {$this->maintenance->quantity}"
                );
            }
            
            $equipment->available_quantity -= $this->maintenance->quantity;
            $equipment->save();
            $this->quantityDeducted = true;
        }
    }

    /**
     * Return quantity to equipment when maintenance ends
     */
    public function returnQuantityOnComplete(): void
    {
        if (!$this->maintenance->equipment) {
            return;
        }

        if (!$this->quantityDeducted) {
            return; // Quantity was never deducted
        }

        $equipment = $this->maintenance->equipment;
        
        // Return quantity if maintenance is completed or end date has passed
        if ($this->maintenance->shouldReturnQuantity()) {
            $equipment->available_quantity += $this->maintenance->quantity;
            
            // Ensure we don't exceed total quantity
            if ($equipment->available_quantity > $equipment->quantity) {
                $equipment->available_quantity = $equipment->quantity;
            }
            
            $equipment->save();
            $this->quantityDeducted = false;
        }
    }

    /**
     * Check if quantity should be deducted
     */
    public function shouldDeductQuantity(): bool
    {
        if ($this->quantityDeducted) {
            return false;
        }
        
        return $this->maintenance->start_date 
            && $this->maintenance->start_date->lte(now())
            && in_array($this->maintenance->status, ['pending', 'in_progress']);
    }

    /**
     * Check if quantity should be returned
     */
    public function shouldReturnQuantity(): bool
    {
        return $this->quantityDeducted && $this->maintenance->shouldReturnQuantity();
    }
}
