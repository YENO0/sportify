<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facility;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+1 week');
        $end = (clone $start)->modify('+1 hour');

        return [
            'facility_id' => Facility::factory(),
            'user_id' => User::factory(),
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'approved',
        ];
    }
}
