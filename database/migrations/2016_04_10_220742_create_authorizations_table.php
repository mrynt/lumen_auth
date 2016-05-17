<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthorizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorizations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('auth');
            $table->string('controller_actions');
            $table->timestamps();
        });

        // Insert some stuff
        DB::table('authorizations')->insert(
            array(
                'auth' => '0',
                'controller_actions' => 'App\Http\Controllers\UserController@me'
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('authorizations');
    }
}
