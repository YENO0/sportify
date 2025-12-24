<?php

namespace App\States\EventStatus;

use App\Models\Event;

class UpcomingState extends BaseEventLifecycleState
{
    public function start(): Event
    {
        $this->event->update(['event_status' => 'Ongoing']);
        return $this->event->refresh();
    }

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

    public function markUpcoming(): Event
    {
        // Idempotent
        return $this->event;
    }
}

