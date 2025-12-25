<?php

namespace App\Notifications;

use App\Models\Facility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class SystemFacilityStatusNotification extends Notification
{
    use Queueable;

    protected $facility;
    protected $closureReason;
    protected $closureDate;

    /**
     * Create a new notification instance.
     */
    public function __construct(Facility $facility, string $closureReason, Carbon $closureDate = null)
    {
        $this->facility = $facility;
        $this->closureReason = $closureReason;
        $this->closureDate = $closureDate ?? Carbon::now();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $facilityName = $this->facility->name;
        $status = $this->closureReason;
        $closureDateFormatted = $this->closureDate->format('d/m/Y');

        return [
            'title' => 'Facility Status Update',
            'sender' => 'System',
            'message' => "The facility '{$facilityName}' status has changed to '{$status}' starting from {$closureDateFormatted}.",
            'facility_id' => $this->facility->id,
            'facility_name' => $facilityName,
            'closure_date' => $closureDateFormatted,
            'status_type' => $status,
        ];
    }
}
