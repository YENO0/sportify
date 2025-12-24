<?php

namespace App\States\Event;

use App\Exceptions\InvalidEventTransitionException;
use App\Models\Event;

abstract class BaseEventState implements EventState
{
    public function __construct(protected Event $event)
    {
    }

    public function approve(?int $approverId = null): Event
    {
        throw $this->invalid('approve');
    }

    public function reject(string $remark, ?int $approverId = null): Event
    {
        throw $this->invalid('reject');
    }

    public function cancel(string $remark): Event
    {
        throw $this->invalid('cancel');
    }

    public function resubmit(array $data): Event
    {
        throw $this->invalid('resubmit');
    }

    protected function invalid(string $action): InvalidEventTransitionException
    {
        return new InvalidEventTransitionException(
            "Cannot {$action} an event while status is '{$this->event->status}'."
        );
    }
}

