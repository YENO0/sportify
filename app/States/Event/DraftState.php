<?php

namespace App\States\Event;

class DraftState extends BaseEventState
{
    // Draft events can be edited and applied (changed to pending)
    // They cannot be approved or rejected until they are applied
}

