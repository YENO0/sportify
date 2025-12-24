<?php

namespace App\Patterns\Decorator;

use App\Models\EquipmentBorrowing;

/**
 * Decorator Pattern - Manager for applying decorators to equipment borrowings
 */
class BorrowingDecoratorManager
{
    /**
     * Apply return scheduler decorator to a borrowing
     */
    public static function withReturnScheduler(EquipmentBorrowing $borrowing): ReturnSchedulerDecorator
    {
        return new ReturnSchedulerDecorator($borrowing);
    }
}

