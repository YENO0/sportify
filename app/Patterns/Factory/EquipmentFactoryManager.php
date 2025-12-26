<?php

namespace App\Patterns\Factory;

use InvalidArgumentException;

/**
 * Factory Manager - Provides a simple way to get the right factory
 */
class EquipmentFactoryManager
{
    /**
     * Get the appropriate factory based on sport type ID
     * Factory Method Pattern: Creates factory based on sport type
     *
     * @param int|null $sportTypeId
     * @return EquipmentFactoryInterface
     * @throws InvalidArgumentException
     */
    public static function getFactory(?int $sportTypeId = null): EquipmentFactoryInterface
    {
        if ($sportTypeId) {
            $sportType = \App\Models\SportType::find($sportTypeId);
            return new SportTypeBasedEquipmentFactory($sportType);
        }
        
        // Fallback to sport type based factory without specific sport type
        return new SportTypeBasedEquipmentFactory();
    }

    /**
     * Create equipment using the appropriate factory
     * Factory Method Pattern: Uses sport type to determine factory behavior
     *
     * @param int|null|string $sportTypeIdOrType Sport type ID (int) or legacy type string ('sports', 'gym', 'outdoor')
     * @param array $data
     * @return \App\Models\Equipment
     */
    public static function create($sportTypeIdOrType, array $data): \App\Models\Equipment
    {
        // Check if it's a numeric string (sport_type_id from form) and convert to int
        // Form inputs come as strings, so "3" should be treated as integer 3, not legacy type
        if (is_string($sportTypeIdOrType) && is_numeric($sportTypeIdOrType)) {
            $sportTypeIdOrType = (int) $sportTypeIdOrType;
        }
        
        // Support legacy type-based approach for backward compatibility
        if (is_string($sportTypeIdOrType)) {
            return self::createLegacy($sportTypeIdOrType, $data);
        }
        
        // Use sport type ID approach (preferred)
        $factory = self::getFactory($sportTypeIdOrType);
        return $factory->createEquipment($data);
    }

    /**
     * Legacy method for backward compatibility
     * @deprecated Use create() with sport_type_id instead
     */
    public static function createLegacy(string $type, array $data): \App\Models\Equipment
    {
        return match (strtolower($type)) {
            'sports' => (new SportsEquipmentFactory())->createEquipment($data),
            'gym' => (new GymEquipmentFactory())->createEquipment($data),
            'outdoor' => (new OutdoorEquipmentFactory())->createEquipment($data),
            default => throw new InvalidArgumentException("Unknown equipment type: {$type}"),
        };
    }
}
