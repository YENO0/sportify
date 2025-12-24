<?php

namespace App\Patterns\Factory;

use InvalidArgumentException;

/**
 * Factory Manager - Provides a simple way to get the right factory
 */
class EquipmentFactoryManager
{
    /**
     * Get the appropriate factory based on equipment type
     *
     * @param string $type
     * @return EquipmentFactoryInterface
     * @throws InvalidArgumentException
     */
    public static function getFactory(string $type): EquipmentFactoryInterface
    {
        return match (strtolower($type)) {
            'sports' => new SportsEquipmentFactory(),
            'gym' => new GymEquipmentFactory(),
            'outdoor' => new OutdoorEquipmentFactory(),
            default => throw new InvalidArgumentException("Unknown equipment type: {$type}"),
        };
    }

    /**
     * Create equipment using the appropriate factory
     *
     * @param string $type
     * @param array $data
     * @return \App\Models\Equipment
     */
    public static function create(string $type, array $data): \App\Models\Equipment
    {
        $factory = self::getFactory($type);
        return $factory->createEquipment($data);
    }
}

