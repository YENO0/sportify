<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Create a minimal facility table so existing event FK works.
     */
    public function up(): void
    {
        if (Schema::hasTable('facility')) {
            return;
        }

        Schema::create('facility', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Seed a few placeholder facilities to match hardcoded UI options.
        DB::table('facility')->insert([
            ['id' => 1, 'name' => 'Main Hall', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Conference Room', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Outdoor Field', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('facility');
    }
};

