<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user_model = config('bdren_oauth.oauth_user_model');
        // get table name from model
        $table_name = (new $user_model)->getTable();
        // check if table exists
        if (!Schema::hasTable($table_name)) {
            throw new \Exception('Table ' . $table_name . ' does not exists');
        }
        // check if table has oauth_token column
        if (Schema::hasColumn($table_name, 'oauth_token')) {
            throw new \Exception('Table ' . $table_name . ' already has oauth_token column');
        }
        // add oauth_token column
        Schema::table($table_name, function (Blueprint $table) {
            $table->string('oauth_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $user_model = config('bdren_oauth.oauth_user_model');
        // get table name from model
        $table_name = (new $user_model)->getTable();

        Schema::table($table_name, function (Blueprint $table) {
            //
        });
    }
};
