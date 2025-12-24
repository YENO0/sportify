<?php

namespace App\Patterns\Decorator;

use App\Models\Equipment;

/**
 * Decorator Pattern - Component Interface
 * Defines the interface for equipment decorators
 */
interface EquipmentDecoratorInterface
{
    /**
     * Get the decorated equipment
     *
     * @return Equipment
     */
    public function getEquipment(): Equipment;

    /**
     * Get the total cost including all decorators
     *
     * @return float
     */
    public function getTotalCost(): float;

    /**
     * Get all features as an array
     *
     * @return array
     */
    public function getFeatures(): array;

    /**
     * Apply the decorator to the equipment
     *
     * @return void
     */
    public function apply(): void;
}

