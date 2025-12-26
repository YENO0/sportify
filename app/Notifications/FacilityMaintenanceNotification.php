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
        $startDate = $this->facilityMaintenance->start_date->format('M d, Y H:i');
        $endDate = $this->facilityMaintenance->end_date->format('M d, Y H:i');
        $maintenanceTitle = $this->facilityMaintenance->title ?? 'Scheduled Maintenance';

        // Role-specific message
        $roleMessage = "The facility '{$facility->name}' has been scheduled for maintenance from {$startDate} to {$endDate}. It will be unavailable during this period.";
        
        if ($notifiable->isAdmin()) {
            $roleMessage = "Facility '{$facility->name}' maintenance scheduled from {$startDate} to {$endDate}. Any approved events or bookings during this period will be automatically cancelled. Please review the facility status in the admin panel.";
        } elseif ($notifiable->isCommittee()) {
            $roleMessage = "Facility '{$facility->name}' will be under maintenance from {$startDate} to {$endDate}. If you have events scheduled at this facility during this period, they will be automatically cancelled. Please check your events and consider rescheduling after the maintenance period.";
        }

        return [
            'title' => "Maintenance Scheduled: {$facility->name}",
            'sender' => 'System',
            'message' => $roleMessage,
            'facility_id' => $facility->id,
            'facility_name' => $facility->name,
            'maintenance_title' => $maintenanceTitle,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status_type' => 'Maintenance',
        ];
    }
}