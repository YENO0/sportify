<?php

namespace App\Patterns\Factory;

use App\Models\Equipment;

/**
 * Factory Method Pattern - Abstract Creator
 * Provides default implementation and template method
 */
abstract class AbstractEquipmentFactory implements EquipmentFactoryInterface
{
    /**
     * Template method that defines the algorithm for creating equipment
     *
     * @param array $data
     * @return Equipment
     */
    public function createEquipment(array $data): Equipment
    {
        // Validate data
        $this->validateData($data);
        
        // Set default values based on equipment type
        $data = $this->setDefaultValues($data);
        
        // Create the equipment
        $equipment = $this->makeEquipment($data);
        
        // Post-creation processing
        $this->postCreation($equipment, $data);
        
        return $equipment;
    }

    /**
     * Validate the input data
     *
     * @param array $data
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateData(array $data): void
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('Equipment name is required');
        }
        
        if (!isset($data['quantity']) || $data['quantity'] < 0) {
            throw new \InvalidArgumentException('Quantity must be a non-negative integer');
        }
    }

    /**
     * Set default values for the equipment type
     *
     * @param array $data
     * @return array
     */
    abstract protected function setDefaultValues(array $data): array;

    /**
     * Create the actual equipment instance
     *
     * @param array $data
     * @return Equipment
     */
    abstract protected function makeEquipment(array $data): Equipment;

    /**
     * Post-creation processing
     *
     * @param Equipment $equipment
     * @param array $data
     * @return void
     */
    protected function postCreation(Equipment $equipment, array $data): void
    {
        // Default implementation - can be overridden
    }
}

