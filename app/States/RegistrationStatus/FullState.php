<?php

namespace App\States\RegistrationStatus;

use App\Models\Event;

class FullState extends BaseRegistrationStatusState
{
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

    public function full(): Event
    {
        // Idempotent
        return $this->event;
    }
}

