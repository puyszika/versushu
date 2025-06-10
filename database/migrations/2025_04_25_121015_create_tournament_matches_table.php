<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('championship_id')->constrained()->onDelete('cascade');
            $table->foreignId('team1_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->foreignId('team2_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->integer('round');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->foreignId('winner_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->dropColumn(['team1_id', 'team2_id', 'winner_id']);
        });
    }
};
