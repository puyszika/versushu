<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('lobbies', function (Blueprint $table) {
        $table->dropForeign(['host_user_id']);
        $table->dropColumn('host_user_id');
    });
}

public function down(): void
{
    Schema::table('lobbies', function (Blueprint $table) {
        $table->foreignId('host_user_id')->nullable()->constrained('users')->onDelete('set null');
    });
}

};
