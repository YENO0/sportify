<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Facility;
use App\Models\Booking;
use App\Models\FacilityMaintenance;
use Illuminate\Support\Facades\Notification;

class FacilityMaintenanceBookingCancellationTest extends TestCase
{
    use RefreshDatabase;

    protected $testUser;
    protected $facility;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testUser = User::factory()->create(['id' => 1]);
        $this->actingAs($this->testUser);
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->facility = Facility::factory()->create(['status' => 'Active']);
    }

    /** @test */
    public function it_cancels_only_overlapping_bookings_when_maintenance_starts()
    {
        Notification::fake();

        // 1. Create a booking that SHOULD be cancelled (overlaps with maintenance)
        // Maintenance: Now to Now + 2 days
        // Booking 1: Now + 1 day (Inside maintenance)
        $bookingToCancel = Booking::factory()->create([
            'facility_id' => $this->facility->id,
            'user_id' => $this->testUser->id,
            'start_time' => now()->addDay()->setHour(10)->setMinute(0),
            'end_time' => now()->addDay()->setHour(11)->setMinute(0),
            'status' => 'approved',
        ]);

        // 2. Create a booking that SHOULD NOT be cancelled (outside maintenance)
        // Booking 2: Now + 5 days (Outside maintenance)
        $bookingToKeep = Booking::factory()->create([
            'facility_id' => $this->facility->id,
            'user_id' => $this->testUser->id,
            'start_time' => now()->addDays(5)->setHour(10)->setMinute(0),
            'end_time' => now()->addDays(5)->setHour(11)->setMinute(0),
            'status' => 'approved',
        ]);

        // 3. Create Maintenance that starts NOW (triggering status change)
        $maintenance = FacilityMaintenance::create([
            'facility_id' => $this->facility->id,
            'title' => 'Urgent Repairs',
            'description' => 'Fixing roof',
            'start_date' => now()->subMinute(), // Started just now
            'end_date' => now()->addDays(2),
        ]);

        // 4. Trigger the logic (The observer 'created' event might set status, but let's ensure status is updated manually if needed to simulate the full flow)
        // The FacilityMaintenanceObserver 'created' method should automatically update the facility status if start_date <= now()
        // which triggers the FacilityObserver 'updated' method.
        
        $this->facility->refresh();
        
        // Ensure facility is in maintenance
        $this->assertEquals('Maintenance', $this->facility->status);

        // 5. Verify Booking Statuses
        $this->assertDatabaseHas('bookings', [
            'id' => $bookingToCancel->id,
            'status' => 'cancelled',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $bookingToKeep->id,
            'status' => 'approved',
        ]);
    }
}
