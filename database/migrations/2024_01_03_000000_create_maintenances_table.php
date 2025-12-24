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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->string('maintenance_type'); // scheduled, emergency, preventive, repair
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('scheduled_date');
            $table->date('completed_date')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->decimal('cost', 10, 2)->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->text('technician_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};

