<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('author');
            $table->unsignedBigInteger('reported_user');
            $table->timestamps();

            // Report references owner user
            $table->foreign('author')
                ->references('id')
                ->on('users');

            // Report references reported user
            $table->foreign('reported_user')
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
        Schema::dropIfExists('profile_reports');
    }
}
