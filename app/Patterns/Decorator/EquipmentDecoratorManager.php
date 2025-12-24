<?php

namespace App\Patterns\Decorator;

use App\Models\Equipment;
use InvalidArgumentException;

/**
 * Decorator Manager - Provides a convenient way to apply decorators
 */
class EquipmentDecoratorManager
{
    protected Equipment $equipment;
    protected array $decorators = [];

    public function __construct(Equipment $equipment)
    {
        $this->equipment = $equipment;
    }

    /**
     * Add insurance decorator
     *
     * @param float $cost
     * @param \Carbon\Carbon|null $expiryDate
     * @return self
     */
    public function withInsurance(float $cost, ?\Carbon\Carbon $expiryDate = null): self
    {
        $this->decorators[] = new InsuranceDecorator($this->equipment, $cost, $expiryDate);
        return $this;
    }

    /**
     * Add warranty decorator
     *
     * @param string $type
     * @param \Carbon\Carbon|null $expiryDate
     * @return self
     */
    public function withWarranty(string $type, ?\Carbon\Carbon $expiryDate = null): self
    {
        $this->decorators[] = new WarrantyDecorator($this->equipment, $type, $expiryDate);
        return $this;
    }

    /**
     * Add maintenance tracking decorator
     *
     * @param int $intervalMonths
     * @return self
     */
    public function withMaintenanceTracking(int $intervalMonths = 3): self
    {
        $this->decorators[] = new MaintenanceTrackingDecorator($this->equipment, $intervalMonths);
        return $this;
    }

    /**
     * Add maintenance history tracking decorator
     *
     * @param bool $enableHistoryTracking
     * @return self
     */
    public function withMaintenanceHistory(bool $enableHistoryTracking = true): self
    {
        $this->decorators[] = new MaintenanceHistoryDecorator($this->equipment, $enableHistoryTracking);
        return $this;
    }

    /**
     * Add low stock alert decorator
     *
     * @param bool $enableAlerts
     * @param string|null $alertEmail
     * @return self
     */
    public function withLowStockAlert(bool $enableAlerts = true, ?string $alertEmail = null): self
    {
        $this->decorators[] = new LowStockAlertDecorator($this->equipment, $enableAlerts, $alertEmail);
        return $this;
    }

    /**
     * Apply all decorators
     *
     * @return Equipment
     */
    public function apply(): Equipment
    {
        foreach ($this->decorators as $decorator) {
            $decorator->apply();
        }
        
        $this->equipment->refresh();
        return $this->equipment;
    }

    /**
     * Get total cost with all decorators
     *
     * @return float
     */
    public function getTotalCost(): float
    {
        $totalCost = (float) ($this->equipment->price ?? 0);
        
        foreach ($this->decorators as $decorator) {
            $totalCost += $decorator->getTotalCost() - (float) ($this->equipment->price ?? 0);
        }
        
        return $totalCost;
    }
}

