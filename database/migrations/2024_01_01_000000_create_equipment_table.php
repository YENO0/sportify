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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // e.g., 'sports', 'gym', 'outdoor'
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->integer('available_quantity');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('status')->default('available'); // available, maintenance, damaged, retired
            $table->string('location')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('equipment_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->string('feature_type'); // e.g., 'insurance', 'warranty', 'maintenance_tracking'
            $table->string('feature_name');
            $table->text('feature_value')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('equipment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->string('transaction_type'); // checkout, return, maintenance, damage_report
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->date('transaction_date');
            $table->date('expected_return_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_transactions');
        Schema::dropIfExists('equipment_features');
        Schema::dropIfExists('equipment');
    }
};

