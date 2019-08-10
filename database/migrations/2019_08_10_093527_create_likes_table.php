<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            // Like values
            $table->bigIncrements('id');
            $table->unsignedBigInteger('post');
            $table->unsignedBigInteger('user');
            $table->timestamps();

            // Like references parent post, on post delete remove like
            $table->foreign('post')
                ->references('id')
                ->on('posts');

            // Like references owner user, on user delete remove like
            $table->foreign('user')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
