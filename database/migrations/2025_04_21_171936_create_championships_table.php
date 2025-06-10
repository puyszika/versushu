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
        Schema::create('championships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable(); // Logó vagy háttér
            $table->string('reward_1')->nullable();
            $table->string('reward_2')->nullable();
            $table->string('reward_3')->nullable();
            $table->string('reward_4')->nullable();
            $table->enum('format', ['BO1', 'BO3', 'BO5'])->default('BO1');
            $table->boolean('double_elimination')->default(false);
            $table->unsignedBigInteger('mvp_user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('championships');
    }
};
