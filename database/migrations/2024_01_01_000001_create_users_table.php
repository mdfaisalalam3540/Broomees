<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username')->unique();
            $table->integer('age');
            $table->decimal('reputation_score', 8, 2)->default(0);
            $table->integer('version')->default(0);
            $table->timestamps();
            $table->index(['reputation_score', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }


};