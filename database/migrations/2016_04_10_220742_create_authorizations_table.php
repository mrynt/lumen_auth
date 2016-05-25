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
            $table->string('object');
            $table->string('field');
            $table->string('own');
            $table->char('store',1);
            $table->char('update',1);
            $table->char('destroy',1);
            $table->char('show',1);
        });

        DB::table('authorizations')->insert(
            array(
                'auth' => '0',
                'object' => 'User',
                'field' => 'username',
                'store'=>2,
                'update'=>2,
                'destroy'=>2,
                'show'=>2,
            )
        );

        DB::table('authorizations')->insert(
            array(
                'auth' => '0',
                'object' => 'User',
                'field' => 'email',
                'store'=>2,
                'update'=>2,
                'destroy'=>2,
                'show'=>2,
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
