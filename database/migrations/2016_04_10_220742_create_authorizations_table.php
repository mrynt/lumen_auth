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
          $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('auth')->unsigned()->nullable();
            $table->string('object');
            $table->string('field');
            $table->string('own');
            $table->char('store',1);
            $table->char('update',1);
            $table->char('destroy',1);
            $table->char('show',1);
        });

        Schema::table('authorizations', function($table){
          $table->foreign('auth')->references('auth')->on('groups');
        });

        DB::table('authorizations')->insert(
            array(
                'auth' => '2',
                'object' => 'User',
                'field' => '*',
                'store'=>2,
                'update'=>2,
                'destroy'=>2,
                'show'=>2,
                'own'=>"id"
            )
        );

        DB::table('authorizations')->insert(
            array(
                'auth' => '2',
                'object' => 'Authorization',
                'field' => '*',
                'store'=>2,
                'update'=>2,
                'destroy'=>2,
                'show'=>2
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
