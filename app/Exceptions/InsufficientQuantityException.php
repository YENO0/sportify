<?php

namespace App\Exceptions;

/**
 * Exception thrown when there's insufficient equipment quantity
 */
class InsufficientQuantityException extends EquipmentException
{
    protected $requestedQuantity;
    protected $availableQuantity;

    public function __construct($equipmentId, int $requestedQuantity, int $availableQuantity, ?\Throwable $previous = null)
    {
        $this->requestedQuantity = $requestedQuantity;
        $this->availableQuantity = $availableQuantity;
        
        $message = "Insufficient quantity. Requested: {$requestedQuantity}, Available: {$availableQuantity}";
        parent::__construct($message, 422, $previous, $equipmentId);
    }

    public function getRequestedQuantity(): int
    {
        return $this->requestedQuantity;
    }

    public function getAvailableQuantity(): int
    {
        return $this->availableQuantity;
    }
}

