<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Facility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FacilityClosureNotification extends Notification
{
    use Queueable;

    public $facility;
    public $booking;
    public $closureReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Facility $facility, Booking $booking, string $closureReason)
    {
        $this->facility = $facility;
        $this->booking = $booking;
        $this->closureReason = $closureReason;
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
        $facilityName = $this->facility->name;
        $status = $this->closureReason;
        $rebookLink = route('/events/create'); // Changed to bookings.index

        return (new MailMessage)
                    ->from(config('mail.from.address'), 'System')
                    ->subject("Rebook your facility")
                    ->greeting("Hello!")
                    ->line("The {$facilityName} is currently unavailable due to {$status}. Your booking from {$this->booking->start_time->format('M d, Y H:i')} to {$this->booking->end_time->format('M d, Y H:i')} has been cancelled.")
                    ->action('Rebook Now', $rebookLink)
                    ->line('Please rebook an alternative facility. We apologize for any inconvenience.');
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
        $rebookLink = route('bookings.index'); // Changed to bookings.index

        return [
            'facility_id' => $this->facility->id,
            'facility_name' => $facilityName,
            'status' => $status,
            'booking_id' => $this->booking->id,
            'message' => "The {$facilityName} is currently unavailable due to {$status}. Your booking has been cancelled.",
            'action_url' => $rebookLink,
            'read_at' => null,
        ];
    }
}