<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Step 1: Create sample users first (admin, committee, student)
        // These are needed by EventSeeder and other seeders
        $this->call([
            \Database\Seeders\SampleUsersSeeder::class,
        ]);

        // Step 2: Seed independent data (no dependencies)
        $this->call([
            \Database\Seeders\SportTypeSeeder::class,
            \Database\Seeders\BrandSeeder::class,
            \Database\Seeders\FacilitySeeder::class,
        ]);

        // Step 3: Seed data that depends on Step 2
        $this->call([
            \Database\Seeders\EquipmentSeeder::class, // Depends on SportType and Brand
        ]);

        // Step 4: Seed events (depends on Users and Facilities)
        $this->call([
            \Database\Seeders\EventSeeder::class, // Depends on Users (admin, committee) and Facilities
        ]);

        // Step 5: Seed event registrations (depends on Events and Users/Students)
        $this->call([
            \Database\Seeders\EventJoinedSeeder::class, // Depends on Events and Users
            // Note: EventRegistrationDummySeeder also seeds eventJoined, 
            // but it depends on Student model which may not exist
        ]);

        // Step 6: Seed payments (depends on eventJoined records)
        $this->call([
            \Database\Seeders\PaymentSeeder::class, // Depends on eventJoined
        ]);

        // Step 7: Seed invoices (depends on payments)
        $this->call([
            \Database\Seeders\InvoiceSeeder::class, // Depends on Payments and eventJoined with paymentID
        ]);

        // Note: DummyUserSeeder is redundant (creates test@example.com which is already in DatabaseSeeder above)
        // Keeping it commented out to avoid confusion, but can be removed if not needed
    }
}
