<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hobby_user', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('hobby_id');
            $table->timestamps();

            $table->primary(['user_id', 'hobby_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('hobby_id')->references('id')->on('hobbies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hobby_user');
    }
};