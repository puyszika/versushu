<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->text('admin_note')->nullable()->after('status');
            $table->foreignId('disqualified_team_id')->nullable()->constrained('teams')->nullOnDelete()->after('admin_note');
        });
    }

    public function down()
    {
        Schema::table('tournament_matches', function (Blueprint $table) {
            $table->dropColumn(['admin_note', 'disqualified_team_id']);
        });
    }
};
