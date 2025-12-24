<?php

namespace App\States\RegistrationStatus;

use App\Models\Event;

class OpenState extends BaseRegistrationStatusState
{
    public function full(): Event
    {
        $this->event->update(['registration_status' => 'Full']);
        return $this->event->refresh();
    }

    public function close(): Event
    {
        $this->event->update(['registration_status' => 'Closed']);
        return $this->event->refresh();
    }

    public function notOpen(): Event
    {
        $this->event->update(['registration_status' => 'NotOpen']);
        return $this->event->refresh();
    }

    public function open(): Event
    {
        // Idempotent
        return $this->event;
    }
}

