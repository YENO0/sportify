<?php

namespace App\Exceptions;

/**
 * Exception thrown when equipment is not found
 */
class EquipmentNotFoundException extends EquipmentException
{
    public function __construct($equipmentId = null, ?\Throwable $previous = null)
    {
        $message = $equipmentId 
            ? "Equipment with ID {$equipmentId} not found."
            : "Equipment not found.";
        parent::__construct($message, 404, $previous, $equipmentId);
    }
}

