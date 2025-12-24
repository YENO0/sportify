<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('scheduled_date');
            $table->date('end_date')->nullable()->after('start_date');
            $table->integer('quantity')->default(1)->after('equipment_id');
            // Rename scheduled_date to be more clear
            // Keep scheduled_date for backward compatibility, but start_date will be the actual start
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'quantity']);
        });
    }
};

