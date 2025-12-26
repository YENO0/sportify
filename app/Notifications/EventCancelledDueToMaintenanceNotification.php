<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\Facility;
use App\Models\FacilityMaintenance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventCancelledDueToMaintenanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $event;
    public $facility;
    public $maintenance;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, Facility $facility, FacilityMaintenance $maintenance)
    {
        $this->event = $event;
        $this->facility = $facility;
        $this->maintenance = $maintenance;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $eventName = $this->event->event_name;
        $facilityName = $this->facility->name;
        $eventDate = $this->event->event_start_date->format('M d, Y');
        $eventTime = $this->event->event_start_time ? \Carbon\Carbon::parse($this->event->event_start_time)->format('g:i A') : 'TBA';
        $maintenanceStart = $this->maintenance->start_date->format('M d, Y');
        $maintenanceEnd = $this->maintenance->end_date->format('M d, Y');
        $maintenanceTitle = $this->maintenance->title ?? 'Scheduled Maintenance';

        return (new MailMessage)
                    ->from(config('mail.from.address'), 'Sportify Events')
                    ->subject("Event Cancelled: {$eventName}")
                    ->greeting("Hello {$notifiable->name}!")
                    ->line("We regret to inform you that the event **{$eventName}** scheduled for **{$eventDate} at {$eventTime}** has been cancelled.")
                    ->line("**Reason for Cancellation:**")
                    ->line("The facility **{$facilityName}** is unavailable due to scheduled maintenance.")
                    ->line("**Maintenance Details:**")
                    ->line("- Maintenance Type: {$maintenanceTitle}")
                    ->line("- Maintenance Period: {$maintenanceStart} to {$maintenanceEnd}")
                    ->line("We apologize for any inconvenience this may cause. If you have made a payment for this event, please contact our support team for a refund.")
                    ->action('View Other Events', route('events.approved'))
                    ->line('Thank you for your understanding.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $eventName = $this->event->event_name;
        $facilityName = $this->facility->name;
        $eventDate = $this->event->event_start_date->format('M d, Y');
        $maintenanceStart = $this->maintenance->start_date->format('M d, Y');
        $maintenanceEnd = $this->maintenance->end_date->format('M d, Y');

        return [
            'event_id' => $this->event->eventID,
            'event_name' => $eventName,
            'facility_id' => $this->facility->id,
            'facility_name' => $facilityName,
            'maintenance_id' => $this->maintenance->id,
            'maintenance_title' => $this->maintenance->title ?? 'Scheduled Maintenance',
            'event_date' => $eventDate,
            'maintenance_period' => "{$maintenanceStart} to {$maintenanceEnd}",
            'message' => "The event \"{$eventName}\" scheduled for {$eventDate} has been cancelled because the facility \"{$facilityName}\" is unavailable due to maintenance from {$maintenanceStart} to {$maintenanceEnd}.",
            'action_url' => route('events.approved'),
            'read_at' => null,
        ];
    }
}

