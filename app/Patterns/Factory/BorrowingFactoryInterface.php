<?php

namespace App\Patterns\Factory;

use App\Models\Equipment;
use App\Models\Event;

/**
 * Factory Method Pattern - Interface for creating equipment borrowings
 */
interface BorrowingFactoryInterface
{
    /**
     * Create a borrowing for equipment and event.
     *
     * @param Equipment $equipment
     * @param Event $event
     * @param array $data
     * @return \App\Models\EquipmentBorrowing
     */
    public function createBorrowing(Equipment $equipment, Event $event, array $data);
}

