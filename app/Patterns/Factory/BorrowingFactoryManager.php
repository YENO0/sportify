<?php

namespace App\Patterns\Factory;

use App\Models\Equipment;
use App\Models\Event;

/**
 * Factory Method Pattern - Manager for creating equipment borrowings
 */
class BorrowingFactoryManager
{
    /**
     * Create a borrowing using the appropriate factory
     */
    public static function create(Equipment $equipment, Event $event, array $data): \App\Models\EquipmentBorrowing
    {
        // For now, we only have event-based borrowing
        // In the future, we could add other types (e.g., RegularBorrowingFactory)
        $factory = new EventBorrowingFactory();
        
        return $factory->createBorrowing($equipment, $event, $data);
    }
}

