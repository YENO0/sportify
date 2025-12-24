<?php

namespace App\Patterns\Decorator;

use App\Models\Equipment;
use Carbon\Carbon;

/**
 * Decorator Pattern - Concrete Decorator for Insurance
 */
class InsuranceDecorator extends BaseEquipmentDecorator
{
    public function __construct(Equipment $equipment, float $insuranceCost, ?Carbon $expiryDate = null)
    {
        parent::__construct($equipment);
        $this->additionalCost = $insuranceCost;
        
        $this->features[] = [
            'type' => 'insurance',
            'name' => 'Equipment Insurance',
            'value' => "Coverage: \${$insuranceCost}",
            'expiry_date' => $expiryDate ?? now()->addYear(),
        ];
    }
}

