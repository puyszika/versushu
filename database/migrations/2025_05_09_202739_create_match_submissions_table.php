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
        Schema::create('match_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('tournament_matches')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->string('result_image_path')->nullable();
            $table->string('custom_result_text')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->unique(['match_id', 'team_id']); // Egy csapat csak egyszer küldhet be egy adott meccsre
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_submissions');
    }
};
