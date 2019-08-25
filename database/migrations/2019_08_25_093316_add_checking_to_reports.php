<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCheckingToReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_reports', function (Blueprint $table) {
            $table->boolean('checked')->default(0);
            $table->dateTime('checked_at')->nullable();
        });
        Schema::table('profile_reports', function (Blueprint $table) {
            $table->boolean('checked')->default(0);
            $table->dateTime('checked_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_reports', function (Blueprint $table) {
            $table->dropColumn('checked');
            $table->dropColumn('checked_at');
        });
        Schema::table('profile_reports', function (Blueprint $table) {
            $table->dropColumn('checked');
            $table->dropColumn('checked_at');
        });
    }
}
