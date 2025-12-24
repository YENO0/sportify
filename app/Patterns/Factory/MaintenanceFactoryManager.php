<?php

namespace App\Patterns\Factory;

use InvalidArgumentException;

/**
 * Maintenance Factory Manager - Provides a simple way to get the right factory
 */
class MaintenanceFactoryManager
{
    /**
     * Get the appropriate factory based on maintenance type
     *
     * @param string $type
     * @return MaintenanceFactoryInterface
     * @throws InvalidArgumentException
     */
    public static function getFactory(string $type): MaintenanceFactoryInterface
    {
        return match (strtolower($type)) {
            'scheduled' => new ScheduledMaintenanceFactory(),
            'emergency' => new EmergencyMaintenanceFactory(),
            'preventive' => new PreventiveMaintenanceFactory(),
            'repair' => new RepairMaintenanceFactory(),
            default => throw new InvalidArgumentException("Unknown maintenance type: {$type}"),
        };
    }

    /**
     * Create maintenance using the appropriate factory
     *
     * @param string $type
     * @param array $data
     * @return \App\Models\Maintenance
     */
    public static function create(string $type, array $data): \App\Models\Maintenance
    {
        $factory = self::getFactory($type);
        return $factory->createMaintenance($data);
    }
}

