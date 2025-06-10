<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->unsignedInteger('match_index')->nullable()->after('round');
        });
    }

    public function down()
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->dropColumn('match_index');
        });
    }
};
