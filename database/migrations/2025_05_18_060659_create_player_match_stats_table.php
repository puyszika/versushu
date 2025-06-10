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
        Schema::create('player_match_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('match_id')->constrained('tournament_matches')->onDelete('cascade');
            $table->string('steam_name');
            $table->string('team_key'); // CT vagy T
            $table->integer('kills')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('deaths')->default(0);
            $table->integer('mvp')->default(0);
            $table->integer('score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_match_stats');
    }
};
