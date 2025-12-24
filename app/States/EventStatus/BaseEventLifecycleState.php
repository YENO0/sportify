<?php

namespace App\States\EventStatus;

use App\Exceptions\InvalidEventTransitionException;
use App\Models\Event;

abstract class BaseEventLifecycleState implements EventLifecycleState
{
    public function __construct(protected Event $event)
    {
    }

    public function markUpcoming(): Event
    {
        throw $this->invalid('markUpcoming');
    }

    public function start(): Event
    {
        throw $this->invalid('start');
    }

    public function complete(): Event
    {
        throw $this->invalid('complete');
    }

    public function cancel(): Event
    {
        throw $this->invalid('cancel');
    }

    protected function invalid(string $action): InvalidEventTransitionException
    {
        return new InvalidEventTransitionException(
            "Cannot {$action} event lifecycle while status is '{$this->event->event_status}'."
        );
    }
}

