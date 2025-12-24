<?php

namespace App\Patterns\Decorator;

use App\Models\Equipment;
use Carbon\Carbon;

/**
 * Decorator Pattern - Concrete Decorator for Warranty
 */
class WarrantyDecorator extends BaseEquipmentDecorator
{
    public function __construct(Equipment $equipment, string $warrantyType, ?Carbon $expiryDate = null)
    {
        parent::__construct($equipment);
        
        $this->features[] = [
            'type' => 'warranty',
            'name' => 'Warranty Coverage',
            'value' => "Type: {$warrantyType}",
            'expiry_date' => $expiryDate ?? now()->addYears(2),
        ];
    }
}

