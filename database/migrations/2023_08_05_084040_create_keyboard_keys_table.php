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
        Schema::create('keyboard_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('key_number'); // The number of the key (1-10)
            $table->boolean('state')->default(false); // 0 for unlit, 1 for lit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keyboard_keys');
    }
};
