<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPersonalInfoToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // User personal profile values
            $table->renameColumn('name', 'username');
            $table->string('profile_pic')->nullable($value = true);
            $table->text('bio')->nullable($value = true);
            $table->json('contact_info')->nullable($value = true);
            $table->json('settings')->nullable($value = true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('username', 'name');
            $table->dropColumn('profile_pic');
            $table->dropColumn('bio');
            $table->dropColumn('contactInfo');
            $table->dropColumn('settings');
        });
    }
}
