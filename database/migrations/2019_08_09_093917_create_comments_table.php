<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            // Comment values
            $table->bigIncrements('id');
            $table->unsignedBigInteger('author');
            $table->text('text')->nullable();
            $table->string('media')->nullable();
            $table->unsignedBigInteger('reply_to')->nullable();
            $table->timestamps();

            // Comment references user owner, on delete of owner remove comments
            $table->foreign('author')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Comment references post parent, on delete of parent delete comments
            $table->foreign('reply_to')
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
        Schema::dropIfExists('comments');
    }
}
