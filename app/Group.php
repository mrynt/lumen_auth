<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\AuthorizationController as Authorization;

class Group extends Model {
  public $timestamps = false;
  protected $guarded = ['id'];

  public function users($auth){
    $group = Authorization::show( "my" , new Group() )->where('description', '=', $auth);
    return Authorization::hasMany( $group, 'App\User', 'auth', 'auth' )->get();
  }

  public function auths($auth){
    $group = Authorization::show( "my" , new Group() )->where('description', '=', $auth);
    return Authorization::hasMany( $group, 'App\Authorization', 'auth', 'auth' )->get();
  }

}
