<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->string('token', 64)->unique();
            $table->string('name')->nullable();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['token', 'expires_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_tokens');
    }
};