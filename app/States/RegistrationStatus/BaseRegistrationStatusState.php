<?php

namespace App\States\RegistrationStatus;

use App\Exceptions\InvalidEventTransitionException;
use App\Models\Event;

abstract class BaseRegistrationStatusState implements RegistrationStatusState
{
    public function __construct(protected Event $event)
    {
    }

    public function notOpen(): Event
    {
        throw $this->invalid('notOpen');
    }

    public function open(): Event
    {
        throw $this->invalid('open');
    }

    public function full(): Event
    {
        throw $this->invalid('full');
    }

    public function close(): Event
    {
        throw $this->invalid('close');
    }

    protected function invalid(string $action): InvalidEventTransitionException
    {
        return new InvalidEventTransitionException(
            "Cannot {$action} registration while status is '{$this->event->registration_status}'."
        );
    }
}

