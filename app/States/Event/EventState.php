<?php

namespace App\States\Event;

use App\Models\Event;

interface EventState
{
    public function approve(?int $approverId = null): Event;

    public function reject(string $remark, ?int $approverId = null): Event;

    public function cancel(string $remark): Event;

    public function resubmit(array $data): Event;
}

