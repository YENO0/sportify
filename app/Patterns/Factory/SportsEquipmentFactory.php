<?php

namespace App\Patterns\Factory;

use App\Models\Equipment;

/**
 * Factory Method Pattern - Concrete Creator for Sports Equipment
 */
class SportsEquipmentFactory extends AbstractEquipmentFactory
{
    protected function setDefaultValues(array $data): array
    {
        $data['type'] = 'sports';
        $data['status'] = $data['status'] ?? 'available';
        $data['available_quantity'] = $data['available_quantity'] ?? $data['quantity'];
        $data['location'] = $data['location'] ?? 'Sports Storage';
        
        // Factory Method: Set default minimum stock for sports equipment (typically 5 units)
        $data['minimum_stock_amount'] = $data['minimum_stock_amount'] ?? ($data['quantity'] > 0 ? max(5, (int)($data['quantity'] * 0.2)) : 5);
        
        return $data;
    }

    protected function makeEquipment(array $data): Equipment
    {
        return Equipment::create($data);
    }

    protected function postCreation(Equipment $equipment, array $data): void
    {
        // Sports equipment might need special handling
        if (isset($data['requires_inspection']) && $data['requires_inspection']) {
            $equipment->update(['status' => 'maintenance']);
        }

        // Factory Method: Handle image uploads for sports equipment
        // Sports equipment images are stored in equipment-specific folders
        if (isset($data['images']) && !empty($data['images'])) {
            $this->processImages($equipment, $data['images'], $data['image_alt_texts'] ?? []);
        }
    }

    /**
     * Process images for sports equipment (Factory Method specific logic)
     */
    protected function processImages(Equipment $equipment, array $images, array $altTexts = []): void
    {
        $imageDecorator = new \App\Patterns\Decorator\ImageDecorator($equipment);
        $imageDecorator->uploadImages($images, $altTexts);
    }
}

