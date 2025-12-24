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
        Schema::table('equipment', function (Blueprint $table) {
            // Add sport_type_id column
            $table->foreignId('sport_type_id')->nullable()->after('type')->constrained('sport_types')->onDelete('set null');
            
            // Keep type column for backward compatibility, but it will be deprecated
            // We'll migrate data from type to sport_type_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropForeign(['sport_type_id']);
            $table->dropColumn('sport_type_id');
        });
    }
};

