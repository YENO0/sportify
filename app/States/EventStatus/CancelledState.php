<?php

namespace App\States\EventStatus;

use App\Models\Event;

class CancelledState extends BaseEventLifecycleState
{
    public function markUpcoming(): Event
    {
        // Allow reinstating if needed
        $this->event->update(['event_status' => 'Upcoming']);
        return $this->event->refresh();
    }
}

