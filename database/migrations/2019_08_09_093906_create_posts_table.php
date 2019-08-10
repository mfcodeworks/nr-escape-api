<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            // Post values
            $table->bigIncrements('id');
            $table->unsignedBigInteger('author');
            $table->string('type');
            $table->string('media')->nullable($value = true);
            $table->text('caption')->nullable($value = true);
            $table->boolean('repost')->nullable($value = true);
            $table->timestamps();

            // Post references user owner, on delete of owner remove posts
            $table->foreign('author')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('posts');
    }
}
