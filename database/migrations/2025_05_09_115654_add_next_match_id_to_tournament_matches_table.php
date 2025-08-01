<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->unsignedBigInteger('next_match_id')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->dropColumn('next_match_id');
        });
    }
};
