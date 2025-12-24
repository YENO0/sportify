<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('eventJoined')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // Recreate table to allow waitlisted status (SQLite lacks ALTER enum)
            DB::statement('CREATE TABLE eventJoined_new (
                eventJoinedID INTEGER PRIMARY KEY AUTOINCREMENT,
                eventID INTEGER NOT NULL,
                studentID INTEGER NOT NULL,
                status TEXT NOT NULL DEFAULT "registered",
                joinedDate DATETIME DEFAULT CURRENT_TIMESTAMP,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                UNIQUE(eventID, studentID)
            )');

            DB::statement('INSERT INTO eventJoined_new (eventID, studentID, status, joinedDate, created_at, updated_at)
                           SELECT eventID, studentID, status, joinedDate, created_at, updated_at FROM eventJoined');

            DB::statement('DROP TABLE eventJoined');
            DB::statement('ALTER TABLE eventJoined_new RENAME TO eventJoined');
        } else {
            // For other drivers, loosen to string to allow waitlisted
            Schema::table('eventJoined', function (Blueprint $table) {
                $table->string('status', 20)->default('registered')->change();
            });
        }
    }

    public function down(): void
    {
        // No-op; keeping flexible status
    }
};

