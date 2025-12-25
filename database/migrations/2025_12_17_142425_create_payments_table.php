<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('paymentID'); // Primary key
            $table->string('paymentMethod');
            $table->dateTime('paymentDate');
            $table->decimal('paymentAmount', 10, 2);
            $table->unsignedBigInteger('eventJoinedID');

            // Foreign key constraint
            $table->foreign('eventJoinedID')       // ✅ corrected column name
                  ->references('eventJoinedID')   // ✅ references PK in EventJoined
                  ->on('eventJoined')           // ✅ table name in snake_case plural
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
