<?php

namespace App\States\EventStatus;

use App\Models\Event;

class CompletedState extends BaseEventLifecycleState
{
    public function markUpcoming(): Event
    {
        // Allow resetting if needed (e.g., data correction)
        $this->event->update(['event_status' => 'Upcoming']);
        return $this->event->refresh();
    }
}

