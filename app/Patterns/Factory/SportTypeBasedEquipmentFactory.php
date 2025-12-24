<?php

namespace App\Patterns\Factory;

use App\Models\Equipment;
use App\Models\SportType;

/**
 * Factory Method Pattern - Concrete Creator for Sport Type Based Equipment
 * This factory creates equipment based on the selected sport type
 */
class SportTypeBasedEquipmentFactory extends AbstractEquipmentFactory
{
    protected ?SportType $sportType = null;

    public function __construct(?SportType $sportType = null)
    {
        $this->sportType = $sportType;
    }

    protected function setDefaultValues(array $data): array
    {
        // Set sport_type_id if provided
        if (isset($data['sport_type_id']) && $this->sportType === null) {
            $this->sportType = SportType::find($data['sport_type_id']);
        }

        // Set status and available quantity defaults
        $data['status'] = $data['status'] ?? 'available';
        $data['available_quantity'] = $data['available_quantity'] ?? $data['quantity'];
        
        // Factory Method: Set location based on sport type name or default
        if ($this->sportType) {
            $data['location'] = $data['location'] ?? $this->sportType->name . ' Storage';
        } else {
            $data['location'] = $data['location'] ?? 'General Storage';
        }
        
        // Factory Method: Set default minimum stock based on sport type
        // Different sports may have different stock requirements
        if (!isset($data['minimum_stock_amount'])) {
            $defaultMinStock = $this->getDefaultMinimumStock();
            $data['minimum_stock_amount'] = $data['quantity'] > 0 
                ? max($defaultMinStock, (int)($data['quantity'] * 0.15)) 
                : $defaultMinStock;
        }
        
        return $data;
    }

    protected function makeEquipment(array $data): Equipment
    {
        return Equipment::create($data);
    }

    protected function postCreation(Equipment $equipment, array $data): void
    {
        // Factory Method: Handle image uploads for equipment
        // Images are stored in equipment-specific folders
        if (isset($data['images']) && !empty($data['images'])) {
            $this->processImages($equipment, $data['images'], $data['image_alt_texts'] ?? []);
        }
    }

    /**
     * Process images for equipment (Factory Method specific logic)
     */
    protected function processImages(Equipment $equipment, array $images, array $altTexts = []): void
    {
        $imageDecorator = new \App\Patterns\Decorator\ImageDecorator($equipment);
        $imageDecorator->uploadImages($images, $altTexts);
    }

    /**
     * Get default minimum stock based on sport type
     * Factory Method: Different sport types may have different default stock requirements
     */
    protected function getDefaultMinimumStock(): int
    {
        if (!$this->sportType) {
            return 5; // Default minimum stock
        }

        // Factory Method: Different sport types can have different default minimum stock
        // This can be customized per sport type
        $sportTypeName = strtolower($this->sportType->name);
        
        // Examples of different defaults based on sport type
        if (strpos($sportTypeName, 'football') !== false || strpos($sportTypeName, 'soccer') !== false) {
            return 10; // Team sports need more stock
        } elseif (strpos($sportTypeName, 'ping pong') !== false || strpos($sportTypeName, 'table tennis') !== false) {
            return 3; // Individual sports need less
        } elseif (strpos($sportTypeName, 'basketball') !== false) {
            return 8; // Team sport
        } else {
            return 5; // Default for other sports
        }
    }
}

