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
        Schema::table('lobbies', function (Blueprint $table) {
            $table->integer('pickban_phase')->default(0); // 0 = nem indult el, 1 = első ban, stb.
            $table->unsignedBigInteger('current_captain_id')->nullable(); // Soron lévő captain user_id
            $table->string('picked_map')->nullable(); // végső picked map
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lobbies', function (Blueprint $table) {
            //
        });
    }
};
