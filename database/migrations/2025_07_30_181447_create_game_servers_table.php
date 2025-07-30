<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('game_servers', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->integer('port');
            $table->enum('status', ['available', 'busy'])->default('available');
            $table->string('token')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_servers');
    }
};
