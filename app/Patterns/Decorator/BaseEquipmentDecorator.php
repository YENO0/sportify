<?php

namespace App\Patterns\Decorator;

use App\Models\Equipment;

/**
 * Decorator Pattern - Base Decorator
 * Provides default implementation for decorators
 */
abstract class BaseEquipmentDecorator implements EquipmentDecoratorInterface
{
    protected Equipment $equipment;
    protected float $additionalCost = 0.0;
    protected array $features = [];

    public function __construct(Equipment $equipment)
    {
        $this->equipment = $equipment;
    }

    public function getEquipment(): Equipment
    {
        return $this->equipment;
    }

    public function getTotalCost(): float
    {
        $baseCost = (float) ($this->equipment->price ?? 0);
        return $baseCost + $this->additionalCost;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    /**
     * Apply the decorator - saves features to database
     *
     * @return void
     */
    public function apply(): void
    {
        foreach ($this->features as $feature) {
            $this->equipment->features()->create([
                'feature_type' => $feature['type'],
                'feature_name' => $feature['name'],
                'feature_value' => $feature['value'] ?? null,
                'expiry_date' => $feature['expiry_date'] ?? null,
            ]);
        }
    }
}

