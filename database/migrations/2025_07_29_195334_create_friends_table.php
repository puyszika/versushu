<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // aki küldi
            $table->foreignId('friend_id')->constrained('users')->onDelete('cascade'); // akit hív
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamps();
            $table->unique(['user_id', 'friend_id']); // egyszer lehet csak barátság
        });
    }

    public function down()
    {
        Schema::dropIfExists('friends');
    }
};
