<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\Facility;
use App\Models\FacilityMaintenance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommitteeEventCancelledNotification extends Notification implements ShouldQueue
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
        $maintenanceDescription = $this->maintenance->description ?? 'No additional details provided.';
        $registeredCount = $this->event->registrations()->where('status', 'registered')->count();

        return (new MailMessage)
                    ->from(config('mail.from.address'), 'Sportify Events')
                    ->subject("Event Cancelled: {$eventName}")
                    ->greeting("Hello {$notifiable->name}!")
                    ->line("Your posted event **{$eventName}** has been cancelled because the facility **{$facilityName}** is unavailable due to maintenance.")
                    ->line("**Event Details:**")
                    ->line("- Event Name: {$eventName}")
                    ->line("- Scheduled Date: {$eventDate} at {$eventTime}")
                    ->line("- Facility: {$facilityName}")
                    ->line("- Registered Participants: {$registeredCount}")
                    ->line("**Maintenance Information:**")
                    ->line("- Maintenance Type: {$maintenanceTitle}")
                    ->line("- Maintenance Period: {$maintenanceStart} to {$maintenanceEnd}")
                    ->line("- Description: {$maintenanceDescription}")
                    ->line("**Important:** All registered participants have been notified of the cancellation. If you need to reschedule this event, please create a new event application after the maintenance period ends.")
                    ->action('View My Events', route('committee.events.index'))
                    ->line('If you have any questions, please contact the administration.');
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
        $registeredCount = $this->event->registrations()->where('status', 'registered')->count();

        return [
            'event_id' => $this->event->eventID,
            'event_name' => $eventName,
            'facility_id' => $this->facility->id,
            'facility_name' => $facilityName,
            'maintenance_id' => $this->maintenance->id,
            'maintenance_title' => $this->maintenance->title ?? 'Scheduled Maintenance',
            'event_date' => $eventDate,
            'maintenance_period' => "{$maintenanceStart} to {$maintenanceEnd}",
            'registered_count' => $registeredCount,
            'message' => "Your posted event \"{$eventName}\" has been cancelled because the facility \"{$facilityName}\" is unavailable due to maintenance from {$maintenanceStart} to {$maintenanceEnd}. {$registeredCount} participant(s) have been notified.",
            'action_url' => route('committee.events.index'),
            'read_at' => null,
        ];
    }
}

