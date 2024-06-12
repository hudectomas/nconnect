<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('session_name');
            $table->integer('capacity');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity');
            $table->timestamps();
        });

        Schema::create('session_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('user_id');  // Added user_id column
            $table->string('session_name');
            $table->string('user_name');
            $table->integer('capacity');
            $table->timestamps();

            // Foreign key definitions
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');  // Foreign key constraint for user_id
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('session_users');
        Schema::dropIfExists('sessions');
    }
}
