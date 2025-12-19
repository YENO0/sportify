<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoiceID');
            $table->unsignedBigInteger('eventJoinedID');
            $table->timestamp('dateTimeGenerated')->useCurrent();

            // If you want foreign key (recommended)
            $table->foreign('eventJoinedID')
                  ->references('eventJoinedID')
                  ->on('eventJoined')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
