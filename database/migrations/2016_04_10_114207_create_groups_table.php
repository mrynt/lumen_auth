<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('groups', function (Blueprint $table) {
        $table->engine = 'InnoDB';
          $table->increments('id');
          $table->integer('auth')->unsigned()->index();
          $table->string('description');
      });

      DB::table('groups')->insert(
          array(
              'auth' => '0',
              'description' => 'Guest',
          )
      );

      DB::table('groups')->insert(
          array(
              'auth' => '1',
              'description' => 'User',
          )
      );

      DB::table('groups')->insert(
          array(
              'auth' => '2',
              'description' => 'Admin',
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
        Schema::drop('groups');
    }
}
