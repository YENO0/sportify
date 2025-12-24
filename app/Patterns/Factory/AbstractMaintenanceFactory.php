<?php

namespace App\Patterns\Factory;

use App\Models\Maintenance;

/**
 * Factory Method Pattern - Abstract Creator for Maintenance
 * Provides default implementation and template method
 */
abstract class AbstractMaintenanceFactory implements MaintenanceFactoryInterface
{
    /**
     * Template method that defines the algorithm for creating maintenance
     *
     * @param array $data
     * @return Maintenance
     */
    public function createMaintenance(array $data): Maintenance
    {
        // Validate data
        $this->validateData($data);
        
        // Set default values based on maintenance type
        $data = $this->setDefaultValues($data);
        
        // Create the maintenance
        $maintenance = $this->makeMaintenance($data);
        
        // Post-creation processing
        $this->postCreation($maintenance, $data);
        
        return $maintenance;
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
        if (empty($data['equipment_id'])) {
            throw new \InvalidArgumentException('Equipment ID is required');
        }
        
        if (empty($data['title'])) {
            throw new \InvalidArgumentException('Maintenance title is required');
        }
        
        if (empty($data['scheduled_date'])) {
            throw new \InvalidArgumentException('Scheduled date is required');
        }
    }

    /**
     * Set default values for the maintenance type
     *
     * @param array $data
     * @return array
     */
    abstract protected function setDefaultValues(array $data): array;

    /**
     * Create the actual maintenance instance
     *
     * @param array $data
     * @return Maintenance
     */
    abstract protected function makeMaintenance(array $data): Maintenance;

    /**
     * Post-creation processing
     *
     * @param Maintenance $maintenance
     * @param array $data
     * @return void
     */
    protected function postCreation(Maintenance $maintenance, array $data): void
    {
        // Default implementation - can be overridden
    }
}

