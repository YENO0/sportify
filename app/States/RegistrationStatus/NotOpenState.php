<?php

namespace App\States\RegistrationStatus;

use App\Models\Event;

class NotOpenState extends BaseRegistrationStatusState
{
    public function open(): Event
    {
        $this->event->update(['registration_status' => 'Open']);
        return $this->event->refresh();
    }

    public function notOpen(): Event
    {
        // Idempotent
        return $this->event;
    }
}

