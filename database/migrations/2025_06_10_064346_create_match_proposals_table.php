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
            Schema::create('match_proposals', function (Blueprint $table) {
        $table->id();
        $table->json('players'); // 10 user ID tömbben
        $table->json('accepted_players')->nullable();
        $table->json('declined_players')->nullable();
        $table->enum('status', ['pending', 'cancelled', 'accepted'])->default('pending');
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_proposals');
    }
};
