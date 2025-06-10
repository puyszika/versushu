<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->unsignedTinyInteger('ct_score')->nullable()->after('duration');
            $table->unsignedTinyInteger('t_score')->nullable()->after('ct_score');
        });
    }

    public function down()
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->dropColumn(['ct_score', 't_score']);
        });
    }
};
