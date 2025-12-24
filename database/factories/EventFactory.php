<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('+1 week', '+1 month');
        $end   = (clone $start)->modify('+2 hours');

        return [
            'event_name'             => $this->faker->sentence(3),
            'event_description'      => $this->faker->paragraph(),
            'event_poster'           => null,
            'event_start_date'       => $start->format('Y-m-d'),
            'event_end_date'         => $end->format('Y-m-d'),
            'event_start_time'       => $start->format('H:i'),
            'event_end_time'         => $end->format('H:i'),
            'registration_due_date'  => (clone $start)->modify('-3 days')->format('Y-m-d'),
            'max_capacity'           => $this->faker->numberBetween(20, 200),
            'price'                  => $this->faker->randomFloat(2, 0, 100),
            'facility_id'            => null,
            'committee_id'           => User::factory(),
            'approved_by'            => null,
            'approved_at'            => null,
            'status'                 => 'pending',
            'registration_status'    => 'NotOpen',
            'event_status'           => 'Upcoming',
            'rejection_remark'       => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status'      => 'approved',
            'registration_status' => 'Open',
            'event_status' => 'Upcoming',
            'approved_by' => User::factory(),
            'approved_at' => now(),
        ]);
    }
}