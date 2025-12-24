<?php

namespace App\Exceptions;

use Exception;

/**
 * Base exception for equipment-related errors
 */
class EquipmentException extends Exception
{
    protected $equipmentId;

    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null, $equipmentId = null)
    {
        parent::__construct($message, $code, $previous);
        $this->equipmentId = $equipmentId;
    }

    public function getEquipmentId()
    {
        return $this->equipmentId;
    }
}

