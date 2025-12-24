<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite doesn't support ALTER ENUM, so we need to recreate the table
            DB::statement('CREATE TABLE events_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                event_name TEXT NOT NULL,
                event_description TEXT,
                event_poster TEXT,
                event_start_date DATE NOT NULL,
                event_end_date DATE,
                registration_due_date DATE,
                max_capacity INTEGER NOT NULL,
                price DECIMAL(10,2) DEFAULT 0,
                facility_id INTEGER,
                committee_id INTEGER NOT NULL,
                approved_by INTEGER,
                status TEXT NOT NULL DEFAULT "pending",
                approved_at TIMESTAMP,
                rejection_remark TEXT,
                created_at TIMESTAMP,
                updated_at TIMESTAMP
            )');

            DB::statement('INSERT INTO events_new SELECT * FROM events');
            DB::statement('DROP TABLE events');
            DB::statement('ALTER TABLE events_new RENAME TO events');
        } else {
            // For other databases, modify the enum
            DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('draft', 'pending', 'approved', 'rejected', 'full') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // Revert to original enum values
            DB::statement('CREATE TABLE events_old (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                event_name TEXT NOT NULL,
                event_description TEXT,
                event_poster TEXT,
                event_start_date DATE NOT NULL,
                event_end_date DATE,
                registration_due_date DATE,
                max_capacity INTEGER NOT NULL,
                price DECIMAL(10,2) DEFAULT 0,
                facility_id INTEGER,
                committee_id INTEGER NOT NULL,
                approved_by INTEGER,
                status TEXT NOT NULL DEFAULT "pending",
                approved_at TIMESTAMP,
                rejection_remark TEXT,
                created_at TIMESTAMP,
                updated_at TIMESTAMP
            )');

            DB::statement('INSERT INTO events_old SELECT * FROM events WHERE status != "draft"');
            DB::statement('DROP TABLE events');
            DB::statement('ALTER TABLE events_old RENAME TO events');
        } else {
            DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'full') DEFAULT 'pending'");
        }
    }
};

