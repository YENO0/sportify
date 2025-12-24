<?php

namespace App\States\Event;

class RejectedState extends BaseEventState
{
    public function resubmit(array $data): \App\Models\Event
    {
        $this->event->update(array_merge(
            $data,
            [
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
                'rejection_remark' => null,
            ]
        ));

        return $this->event->refresh();
    }

    public function cancel(string $remark): \App\Models\Event
    {
        // Already rejected; just update the remark and clear approvals.
        $this->event->update([
            'rejection_remark' => $remark,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return $this->event->refresh();
    }
}

