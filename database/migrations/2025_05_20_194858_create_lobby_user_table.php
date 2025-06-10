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
        Schema::create('lobby_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lobby_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role',['ct','t','spectator']);
            $table->tinyInteger('slot_index'); // 0–4 a játékosoknál; 0–1 nézőknél
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lobby_user');
    }
};
