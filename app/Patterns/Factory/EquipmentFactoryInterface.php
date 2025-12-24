<?php

namespace App\Patterns\Factory;

use App\Models\Equipment;

/**
 * Factory Method Pattern - Creator Interface
 * Defines the interface for creating equipment objects
 */
interface EquipmentFactoryInterface
{
    /**
     * Create an equipment instance
     *
     * @param array $data
     * @return Equipment
     */
    public function createEquipment(array $data): Equipment;
}

