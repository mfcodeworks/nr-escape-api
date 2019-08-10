<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            // Notification values
            $table->bigIncrements('id');
            $table->unsignedBigInteger('for_author');
            $table->unsignedBigInteger('from_user');
            $table->unsignedBigInteger('post_id');
            $table->string('type');
            $table->timestamps();

            // Notification references owner user, on owner delete remove notification
            $table->foreign('for_author')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Notification references owner user, on owner delete remove notification
            $table->foreign('from_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Notification references parent post, on parent delete remove notification
            $table->foreign('post_id')
                ->references('id')
                ->on('posts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
