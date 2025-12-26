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
    
    // Store essential data as properties to ensure they're available after serialization
    protected $eventName;
    protected $facilityName;
    protected $eventDate;
    protected $maintenanceStart;
    protected $maintenanceEnd;
    protected $maintenanceTitle;
    public $notificationTitle; // Explicitly store the title

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, Facility $facility, FacilityMaintenance $maintenance)
    {
        $this->event = $event;
        $this->facility = $facility;
        $this->maintenance = $maintenance;
        
        // Store essential data immediately to ensure it's available after serialization
        $this->eventName = $event->event_name ?? 'Unknown Event';
        $this->facilityName = $facility->name ?? 'Unknown Facility';
        $this->eventDate = $event->event_start_date ? $event->event_start_date->format('M d, Y') : 'TBA';
        $this->maintenanceStart = $maintenance->start_date ? $maintenance->start_date->format('M d, Y') : 'TBA';
        $this->maintenanceEnd = $maintenance->end_date ? $maintenance->end_date->format('M d, Y') : 'TBA';
        $this->maintenanceTitle = $maintenance->title ?? 'Scheduled Maintenance';
        
        // Explicitly declare and store the title variable
        $this->notificationTitle = "Event Registration Cancelled: {$this->eventName}";
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
                    ->subject("Event Registration Cancelled: {$eventName}")
                    ->greeting("Hello {$notifiable->name}!")
                    ->line("**Your event registration has been cancelled.**")
                    ->line("We regret to inform you that the event **{$eventName}** that you registered for, scheduled for **{$eventDate} at {$eventTime}**, has been cancelled.")
                    ->line("**Cancellation Reason:**")
                    ->line("The facility **{$facilityName}** where this event was to be held is unavailable due to scheduled maintenance.")
                    ->line("**Maintenance Information:**")
                    ->line("- Type: {$maintenanceTitle}")
                    ->line("- Period: {$maintenanceStart} to {$maintenanceEnd}")
                    ->line("**What This Means For You:**")
                    ->line("Your registration for this event has been automatically cancelled. If you have made a payment for this event, you will receive a full refund. Please allow 5-7 business days for the refund to be processed.")
                    ->line("We encourage you to explore other upcoming events that may interest you.")
                    ->action('Browse Other Events', route('events.approved'))
                    ->line('We apologize for any inconvenience and thank you for your understanding.')
                    ->line('**Note:** The event organizer (committee) will handle rescheduling if they choose to do so. You do not need to take any action regarding this event.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Use stored properties first (available after serialization), fallback to model properties
        $eventName = $this->eventName ?? ($this->event->event_name ?? 'Unknown Event');
        $facilityName = $this->facilityName ?? ($this->facility->name ?? 'Unknown Facility');
        $eventDate = $this->eventDate ?? ($this->event->event_start_date ? $this->event->event_start_date->format('M d, Y') : 'TBA');
        $maintenanceStart = $this->maintenanceStart ?? ($this->maintenance->start_date ? $this->maintenance->start_date->format('M d, Y') : 'TBA');
        $maintenanceEnd = $this->maintenanceEnd ?? ($this->maintenance->end_date ? $this->maintenance->end_date->format('M d, Y') : 'TBA');
        $maintenanceTitle = $this->maintenanceTitle ?? ($this->maintenance->title ?? 'Scheduled Maintenance');

        // Use the explicitly stored title variable, or generate it if not available
        $title = $this->notificationTitle ?? "Event Registration Cancelled: {$eventName}";

        return [
            'title' => $title, // Explicitly use the stored title variable
            'sender' => 'System',
            'event_id' => $this->event->eventID ?? null,
            'event_name' => $eventName,
            'facility_id' => $this->facility->id ?? null,
            'facility_name' => $facilityName,
            'maintenance_id' => $this->maintenance->id ?? null,
            'maintenance_title' => $maintenanceTitle,
            'event_date' => $eventDate,
            'maintenance_period' => "{$maintenanceStart} to {$maintenanceEnd}",
            'message' => "Your registration for the event \"{$eventName}\" scheduled for {$eventDate} has been cancelled. The facility \"{$facilityName}\" is unavailable due to maintenance from {$maintenanceStart} to {$maintenanceEnd}. If you made a payment, you will receive a refund.",
            'action_url' => route('events.approved'),
            'notification_type' => 'event_cancelled_student', // Identifier to distinguish from committee notifications
            'read_at' => null,
        ];
    }
}

