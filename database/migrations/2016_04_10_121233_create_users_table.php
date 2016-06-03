<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('users', function (Blueprint $table) {
          $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('username');
            $table->string('password');
            $table->string('salt', 16);
            $table->string('email');
            $table->boolean('confirmed');
            $table->integer('auth')->unsigned()->nullable();
            $table->string('api_token', 60)->unique();
            $table->dateTime('expires_at');
            $table->timestamps();
        });

        Schema::table('users', function($table){
          $table->foreign('auth')->references('auth')->on('groups');
        });

        DB::table('users')->insert(
            array(
                "username" => 'marcocastignoli',
                "password" => '464e30752e1e15de0b9418e79cb8800cf982839b',
                "salt" => 'Vj1uBpmJPHYufBjQ',
                "email" => 'marco.castignoli@gmail.com',
                "confirmed" => '1',
                "auth" => '2',
                "api_token" => 'v57h16jtqYpmu1ynS0HsLDFBvH1G6eNOYLJhlVTnVlV7ETAELTPa98VDOIUu',
                "expires_at" => '2016-06-16 10:01:12'
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
        Schema::drop('users');
    }
}
