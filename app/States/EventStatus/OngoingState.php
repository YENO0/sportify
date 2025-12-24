<?php

namespace App\States\EventStatus;

use App\Models\Event;

class OngoingState extends BaseEventLifecycleState
{
    public function complete(): Event
    {
        $this->event->update(['event_status' => 'Completed']);
        return $this->event->refresh();
    }

    public function cancel(): Event
    {
        $this->event->update(['event_status' => 'Cancelled']);
        return $this->event->refresh();
    }

    public function start(): Event
    {
        // Idempotent
        return $this->event;
    }
}

