<?php

namespace App\Patterns\Factory;

use App\Models\Maintenance;

/**
 * Factory Method Pattern - Creator Interface for Maintenance
 * Defines the interface for creating maintenance objects
 */
interface MaintenanceFactoryInterface
{
    /**
     * Create a maintenance instance
     *
     * @param array $data
     * @return Maintenance
     */
    public function createMaintenance(array $data): Maintenance;
}

