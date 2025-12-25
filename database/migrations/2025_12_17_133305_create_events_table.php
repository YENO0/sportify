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
        Schema::create('events', function (Blueprint $table) {
            $table->id('eventID'); // eventID 

            // Event basic details
            $table->string('event_name');
            $table->text('event_description')->nullable();
            $table->string('event_poster')->nullable();
            
            // Event schedule
            $table->date('event_start_date');
            $table->time('event_start_time')->nullable();
            $table->date('event_end_date')->nullable();
            $table->time('event_end_time')->nullable();
            $table->date('registration_due_date')->nullable();
            
            // Capacity & venue
            $table->integer('max_capacity');
            $table->unsignedBigInteger('facility_id')->nullable();
            
            // Event entry price
            $table->decimal('price', 10, 2)->default(0);
            
            // Ownership & approval
            $table->unsignedBigInteger('committee_id');
            $table->unsignedBigInteger('approved_by')->nullable();

            // Event status (using string for SQLite compatibility)
            $table->string('status')->default('pending'); // draft, pending, approved, rejected, full
            $table->string('registration_status')->default('NotOpen'); // NotOpen, Open, Full, Closed
            $table->string('event_status')->default('Upcoming'); // Upcoming, Ongoing, Completed, Cancelled

            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_remark')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};