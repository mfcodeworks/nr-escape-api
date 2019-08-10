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
            $table->text('text')->nullable($value = true);
            $table->string('media')->nullable($value = true);
            $table->unsignedBigInteger('reply_to')->nullable($value = true);
            $table->timestamps();

            // Comment references user owner, on delete of owner remove comments
            $table->foreign('author')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Comment references comment parent, on delete of parent delete comments
            $table->foreign('reply_to')
                ->references('id')
                ->on('comments')
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
