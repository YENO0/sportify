<?php

namespace App\States\Event;

class PendingState extends BaseEventState
{
    public function approve(?int $approverId = null): \App\Models\Event
    {
        $this->event->update([
            'status' => 'approved',
            'approved_by' => $approverId,
            'approved_at' => now(),
            'rejection_remark' => null,
        ]);

        return $this->event->refresh();
    }

    public function reject(string $remark, ?int $approverId = null): \App\Models\Event
    {
        $this->event->update([
            'status' => 'rejected',
            'rejection_remark' => $remark,
            'approved_by' => $approverId,
            'approved_at' => now(),
        ]);

        return $this->event->refresh();
    }

    public function cancel(string $remark): \App\Models\Event
    {
        $this->event->update([
            'status' => 'rejected',
            'rejection_remark' => $remark,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return $this->event->refresh();
    }
}

