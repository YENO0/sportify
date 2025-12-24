<?php

namespace App\States\RegistrationStatus;

use App\Models\Event;

interface RegistrationStatusState
{
    public function notOpen(): Event;

    public function open(): Event;

    public function full(): Event;

    public function close(): Event;
}

