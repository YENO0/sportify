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
        $this->notificationTitle = "Your Event Has Been Cancelled: {$this->eventName}";
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
        $waitlistedCount = $this->event->registrations()->where('status', 'waitlisted')->count();

        return (new MailMessage)
                    ->from(config('mail.from.address'), 'Sportify Events')
                    ->subject("Your Event Has Been Cancelled: {$eventName}")
                    ->greeting("Hello {$notifiable->name}!")
                    ->line("**Your posted event has been cancelled.**")
                    ->line("The event **{$eventName}** that you created has been automatically cancelled due to facility unavailability.")
                    ->line("**Your Event Details:**")
                    ->line("- Event Name: {$eventName}")
                    ->line("- Scheduled Date & Time: {$eventDate} at {$eventTime}")
                    ->line("- Facility: {$facilityName}")
                    ->line("- Registered Participants: {$registeredCount}")
                    ->line("- Waitlisted Participants: {$waitlistedCount}")
                    ->line("**Reason for Cancellation:**")
                    ->line("The facility **{$facilityName}** is unavailable due to scheduled maintenance.")
                    ->line("**Maintenance Details:**")
                    ->line("- Type: {$maintenanceTitle}")
                    ->line("- Period: {$maintenanceStart} to {$maintenanceEnd}")
                    ->line("- Description: {$maintenanceDescription}")
                    ->line("**What You Need to Know:**")
                    ->line("All registered and waitlisted participants ({$registeredCount} registered, {$waitlistedCount} waitlisted) have been automatically notified of this cancellation. Refunds will be processed for any payments made.")
                    ->line("**Next Steps - Action Required:**")
                    ->line("If you wish to reschedule this event, you must **create a new event application** after the maintenance period ends ({$maintenanceEnd}). The cancelled event cannot be restored or reactivated.")
                    ->line("To reapply:")
                    ->line("1. Wait until after {$maintenanceEnd} (when maintenance is complete)")
                    ->line("2. Go to 'My Events' and click 'Create New Event'")
                    ->line("3. Submit a new event application with your preferred dates")
                    ->action('Create New Event', route('events.create'))
                    ->line('If you have any questions or concerns, please contact the administration team.');
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
        
        // Get registration counts (these need to be calculated from the model)
        $registeredCount = $this->event->registrations()->where('status', 'registered')->count();
        $waitlistedCount = $this->event->registrations()->where('status', 'waitlisted')->count();

        // Use the explicitly stored title variable, or generate it if not available
        $title = $this->notificationTitle ?? "Your Event Has Been Cancelled: {$eventName}";

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
            'registered_count' => $registeredCount,
            'waitlisted_count' => $waitlistedCount,
            'message' => "Your posted event \"{$eventName}\" scheduled for {$eventDate} has been cancelled. The facility \"{$facilityName}\" is unavailable due to maintenance from {$maintenanceStart} to {$maintenanceEnd}. All {$registeredCount} registered and {$waitlistedCount} waitlisted participants have been notified and will receive refunds if applicable. To reschedule, you must create a new event application after {$maintenanceEnd}.",
            'action_url' => route('events.create'), // Changed to create new event (reapply)
            'notification_type' => 'event_cancelled_committee', // Identifier to distinguish from student notifications
            'read_at' => null,
        ];
    }
}

