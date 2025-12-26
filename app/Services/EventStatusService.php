<?php

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;

class EventStatusService
{
    /**
     * Synchronize registration_status and event_status based on rules.
     */
    public static function sync(Event $event): Event
    {
        $event = $event->fresh();

        // Ensure defaults to avoid invalid state resolution
        $normalizedLifecycle = [
            'upcoming' => 'Upcoming',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            // Backward/seed compatibility: some older seed data used "Past"
            'past' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        $normalizedRegistration = [
            'notopen' => 'NotOpen',
            'open' => 'Open',
            'full' => 'Full',
            'closed' => 'Closed',
        ];

        if (empty($event->event_status) && $event->event_status !== '0') {
            $event->event_status = 'Upcoming';
        } elseif (isset($normalizedLifecycle[strtolower($event->event_status)])) {
            $event->event_status = $normalizedLifecycle[strtolower($event->event_status)];
        }

        if (empty($event->registration_status) && $event->registration_status !== '0') {
            $event->registration_status = 'NotOpen';
        } elseif (isset($normalizedRegistration[strtolower($event->registration_status)])) {
            $event->registration_status = $normalizedRegistration[strtolower($event->registration_status)];
        }

        if ($event->isDirty(['event_status', 'registration_status'])) {
            $event->save();
        }

        // Sync event lifecycle status (upcoming/ongoing/completed)
        self::syncEventLifecycle($event);

        // Sync registration status (notopen/open/full/closed)
        self::syncRegistrationStatus($event);

        return $event->fresh();
    }

    /**
     * Update all events (intended for scheduler/maintenance).
     */
    public static function syncAll(): void
    {
        Event::withCount([
            'registrations as registrations_count' => function ($q) {
                $q->where('status', 'registered');
            },
        ])->chunkById(200, function ($events) {
            foreach ($events as $event) {
                self::sync($event);
            }
        });
    }

    protected static function syncEventLifecycle(Event $event): void
    {
        // Keep cancelled as-is
        if ($event->event_status === 'Cancelled') {
            return;
        }

        $today = Carbon::today();
        $start = Carbon::parse($event->event_start_date);
        $end = $event->event_end_date ? Carbon::parse($event->event_end_date) : $start;

        if ($today->gt($end)) {
            if ($event->event_status !== 'Completed') {
                $event->eventLifecycleState()->complete();
                // Unbook the facility when event is completed
                $event->unbookFacility();
                
                // Update facility status if no upcoming/ongoing events remain
                if ($event->facility_id) {
                    $facility = \App\Models\Facility::find($event->facility_id);
                    if ($facility) {
                        $facility->updateStatusBasedOnEvents();
                    }
                }
            }
            return;
        }

        if ($today->isSameDay($start)) {
            if ($event->event_status !== 'Ongoing') {
                $event->eventLifecycleState()->start();
            }
            return;
        }

        if ($today->lt($start) && $event->event_status !== 'Upcoming') {
            $event->eventLifecycleState()->markUpcoming();
        }
    }

    protected static function syncRegistrationStatus(Event $event): void
    {
        // Registration is only available for approved events that are not completed/cancelled
        if ($event->status !== 'approved' || in_array($event->event_status, ['Completed', 'Cancelled'])) {
            if ($event->registration_status !== 'NotOpen') {
                $event->registrationState()->notOpen();
            }
            return;
        }

        $today = Carbon::today();

        if ($event->registration_due_date && $today->gt(Carbon::parse($event->registration_due_date))) {
            if ($event->registration_status !== 'Closed') {
                $event->registrationState()->close();
            }
            return;
        }

        $registered = $event->registrations_count
            ?? $event->registrations()->where('status', 'registered')->count();

        if ($registered >= $event->max_capacity) {
            if ($event->registration_status !== 'Full') {
                $event->registrationState()->full();
            }
            return;
        }

        if ($event->registration_status !== 'Open') {
            $event->registrationState()->open();
        }
    }
}

