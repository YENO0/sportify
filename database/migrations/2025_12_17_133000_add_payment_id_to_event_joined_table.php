<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventJoined', function (Blueprint $table) {
            $table->unsignedBigInteger('paymentID')->nullable()->after('studentID');
        });
    }

    public function down(): void
    {
        Schema::table('eventJoined', function (Blueprint $table) {
            $table->dropColumn('paymentID');
        });
    }
};

