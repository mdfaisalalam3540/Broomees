<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('relationships', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->uuid('friend_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('friend_id')->references('id')->on('users')->onDelete('cascade');

            // Composite unique constraint to prevent duplicates
            $table->unique(['user_id', 'friend_id']);

            // Index for reverse lookup
            $table->unique(['friend_id', 'user_id']);
        });

        // Create trigger/function for mutual relationship (PostgreSQL example)
        if (config('database.default') === 'pgsql') {
            DB::statement('
                CREATE OR REPLACE FUNCTION check_relationship_mutual()
                RETURNS TRIGGER AS $$
                BEGIN
                    IF EXISTS (
                        SELECT 1 FROM relationships
                        WHERE user_id = NEW.friend_id
                        AND friend_id = NEW.user_id
                    ) THEN
                        RAISE EXCEPTION \'Mutual relationship already exists\';
                    END IF;
                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;
            ');

            DB::statement('
                CREATE TRIGGER ensure_mutual_relationship
                BEFORE INSERT ON relationships
                FOR EACH ROW
                EXECUTE FUNCTION check_relationship_mutual();
            ');
        }
    }

    public function down()
    {
        Schema::dropIfExists('relationships');
    }
};