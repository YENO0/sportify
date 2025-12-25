<?php

namespace App\Notifications;

use App\Models\FacilityMaintenance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FacilityMaintenanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $facilityMaintenance;

    /**
     * Create a new notification instance.
     */
    public function __construct(FacilityMaintenance $facilityMaintenance)
    {
        $this->facilityMaintenance = $facilityMaintenance;
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
        $facility = $this->facilityMaintenance->facility;
        $startDate = $this->facilityMaintenance->start_date->format('d/m/Y H:i');
        $endDate = $this->facilityMaintenance->end_date->format('d/m/Y H:i');

        return [
            'title' => "Maintenance Scheduled: {$facility->name}",
            'sender' => 'System',
            'message' => "The facility '{$facility->name}' has been scheduled for maintenance from {$startDate} to {$endDate}. It will be unavailable during this period.",
            'facility_id' => $facility->id,
            'facility_name' => $facility->name,
            'closure_date' => $startDate,
            'status_type' => 'Maintenance',
        ];
    }
}