<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('author');
            $table->unsignedBigInteger('reported_post');
            $table->timestamps();

            // Report references owner user
            $table->foreign('author')
                ->references('id')
                ->on('users');

            // Report references reported user
            $table->foreign('reported_post')
                ->references('id')
                ->on('posts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_reports');
    }
}
