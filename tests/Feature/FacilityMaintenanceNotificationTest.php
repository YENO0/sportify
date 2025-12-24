<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Facility;
use App\Models\FacilityMaintenance;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FacilityMaintenanceNotification;
use Carbon\Carbon;

class FacilityMaintenanceNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->withoutMiddleware();
    }

    /** @test */
    public function it_sends_notification_and_updates_status_when_maintenance_starts_immediately()
    {
        Notification::fake();

        $facility = Facility::factory()->create(['status' => 'Active']);

        $start = now();
        $end = now()->addDays(2);

        $response = $this->actingAs($this->user)->post(route('facilities.maintenance.store'), [
            'facility_id' => $facility->id,
            'title' => 'Urgent Repair',
            'description' => 'Fixing the roof',
            'start_date' => $start->format('Y-m-d H:i:s'),
            'end_date' => $end->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();
        
        // Assert Notification Sent
        Notification::assertSentTo(
            [$this->user],
            FacilityMaintenanceNotification::class
        );

        // Assert Facility Status Updated to Maintenance
        $this->assertEquals('Maintenance', $facility->fresh()->status);
    }

    /** @test */
    public function it_sends_notification_but_does_not_update_status_when_maintenance_is_future()
    {
        Notification::fake();

        $facility = Facility::factory()->create(['status' => 'Active']);

        $start = now()->addDay();
        $end = now()->addDays(2);

        $response = $this->actingAs($this->user)->post(route('facilities.maintenance.store'), [
            'facility_id' => $facility->id,
            'title' => 'Future Repair',
            'description' => 'Painting',
            'start_date' => $start->format('Y-m-d H:i:s'),
            'end_date' => $end->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect();

        // Assert Notification Sent
        Notification::assertSentTo(
            [$this->user],
            FacilityMaintenanceNotification::class
        );

        // Assert Facility Status remains Active
        $this->assertEquals('Active', $facility->fresh()->status);
    }

    /** @test */
    public function command_updates_status_when_maintenance_start_time_is_reached()
    {
        $facility = Facility::factory()->create(['status' => 'Active']);
        
        $start = now()->addHour();
        $end = now()->addDays(1);

        // Create future maintenance directly
        FacilityMaintenance::create([
            'facility_id' => $facility->id,
            'title' => 'Future Work',
            'start_date' => $start,
            'end_date' => $end,
        ]);

        // Status should still be Active
        $this->assertEquals('Active', $facility->fresh()->status);

        // Fast forward time to start date
        Carbon::setTestNow($start);

        // Run the command
        $this->artisan('facilities:update-status')
             ->assertExitCode(0);

        // Assert Facility Status is now Maintenance
        $this->assertEquals('Maintenance', $facility->fresh()->status);
    }

    /** @test */
    public function command_reverts_status_when_maintenance_end_time_is_reached()
    {
        $facility = Facility::factory()->create(['status' => 'Maintenance']);
        
        $start = now()->subDays(1);
        $end = now()->subHour();

        // Create past maintenance directly
        FacilityMaintenance::create([
            'facility_id' => $facility->id,
            'title' => 'Past Work',
            'start_date' => $start,
            'end_date' => $end,
        ]);

        // Status is still Maintenance because we manually created it that way (or it stuck)
        
        // Run the command
        $this->artisan('facilities:update-status')
             ->assertExitCode(0);

        // Assert Facility Status is now Active
        $this->assertEquals('Active', $facility->fresh()->status);
    }
}
