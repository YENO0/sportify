<?php

namespace App\States\EventStatus;

use App\Models\Event;

interface EventLifecycleState
{
    public function markUpcoming(): Event;

    public function start(): Event;

    public function complete(): Event;

    public function cancel(): Event;
}

