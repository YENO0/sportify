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
            $table->id(); // eventID 

            // Event basic details
            $table->string('event_name');
            $table->text('event_description')->nullable();
            $table->string('event_poster')->nullable();
            // Event schedule
            $table->date('event_start_date');
            $table->date('event_end_date')->nullable();
            $table->date('registration_due_date')->nullable();
            // Capacity & venue
            $table->integer('max_capacity');
            // Facility is not ready yet; keep nullable for now
            $table->unsignedBigInteger('facility_id')->nullable();
            // Ownership & approval
            $table->unsignedBigInteger('committee_id');
            $table->unsignedBigInteger('approved_by')->nullable();

            // Event status
            $table->enum('status', ['pending', 'approved', 'rejected', 'full'])
                  ->default('pending');

            $table->timestamp('approved_at')->nullable();

            // Timestamps
            $table->timestamps();

            // Foreign keys (optional but recommended)
            // $table->foreign('facility_id')->references('id')->on('facility');
            $table->foreign('committee_id')->references('id')->on('user');
            $table->foreign('approved_by')->references('id')->on('user');
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
