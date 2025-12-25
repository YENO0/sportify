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
        Schema::create('eventJoined', function (Blueprint $table) {
            $table->id('eventJoinedID');

            // References
            $table->unsignedBigInteger('eventID');
            $table->unsignedBigInteger('studentID');
            $table->unsignedBigInteger('paymentID');

            // Registration status
            $table->enum('status', ['registered', 'cancelled'])
                  ->default('registered');

            // When student joined the event
            $table->timestamp('joinedDate')->useCurrent();

            $table->timestamps();

            /*
             |------------------------------------------
             | Constraints
             |------------------------------------------
             */

            // Prevent duplicate registration
            $table->unique(['eventID', 'studentID']);

            // Foreign key to events table
            $table->foreign('eventID')
                  ->references('eventID')
                  ->on('events')
                  ->onDelete('cascade');

            // studentID FK intentionally NOT enforced yet
            // (Student module not implemented)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventJoined');
    }
};