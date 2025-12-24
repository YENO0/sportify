<?php

namespace App\Exceptions;

/**
 * Exception thrown when equipment status prevents an operation
 */
class EquipmentStatusException extends EquipmentException
{
    protected $currentStatus;
    protected $requiredStatus;

    public function __construct($equipmentId, string $currentStatus, string $requiredStatus = 'available', ?\Throwable $previous = null)
    {
        $this->currentStatus = $currentStatus;
        $this->requiredStatus = $requiredStatus;
        
        $message = "Equipment status is '{$currentStatus}' but operation requires '{$requiredStatus}'.";
        parent::__construct($message, 422, $previous, $equipmentId);
    }

    public function getCurrentStatus(): string
    {
        return $this->currentStatus;
    }

    public function getRequiredStatus(): string
    {
        return $this->requiredStatus;
    }
}

