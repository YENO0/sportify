<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Drop foreign key constraints related to users
            $table->dropForeign(['committee_id']);
            $table->dropForeign(['approved_by']);
        });
    }

    public function down(): void
    {
        // We intentionally do NOT restore them here
        // because the users table is not implemented yet
    }
};
