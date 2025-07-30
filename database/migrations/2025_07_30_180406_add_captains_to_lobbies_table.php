<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lobbies', function (Blueprint $table) {
            $table->unsignedBigInteger('ct_captain_id')->nullable()->after('status');
            $table->unsignedBigInteger('t_captain_id')->nullable()->after('ct_captain_id');
        });
    }

    public function down()
    {
        Schema::table('lobbies', function (Blueprint $table) {
            $table->dropColumn(['ct_captain_id', 't_captain_id']);
        });
    }
};
