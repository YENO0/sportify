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
        Schema::create('event_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_member_id')->constrained('users')->onDelete('cascade'); // Foreign key to users table
            $table->string('event_name');
            $table->text('description');
            $table->date('event_date');
            $table->decimal('proposed_budget', 10, 2); // 10 total digits, 2 after decimal
            $table->string('status')->default('pending'); // e.g., 'pending', 'approved', 'rejected'
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_applications');
    }
};
