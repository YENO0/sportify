<?php

namespace App\States\RegistrationStatus;

use App\Models\Event;

class ClosedState extends BaseRegistrationStatusState
{
    public function notOpen(): Event
    {
        $this->event->update(['registration_status' => 'NotOpen']);
        return $this->event->refresh();
    }

    public function close(): Event
    {
        // Idempotent
        return $this->event;
    }
}

