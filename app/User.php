<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use App\Http\Controllers\AuthorizationController as Authorization;
use Illuminate\Pagination\Paginator as Paginator;
class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    protected $guarded = ['id'];
    protected $hidden = [
        'salt'
    ];

    public static function group($id){
        $user = self::show("my")->find($id);
        return Authorization::belongsTo($user, 'App\Group','auth', 'auth');
    }

    public static function authorizations($id){
      $user = self::show("my")->find($id);
      return Authorization::hasManyThrough($user, 'App\Authorization', 'App\Group'  ,'auth', 'auth');
    }

    public static function show($type){
      return Authorization::show( $type , new User() );
    }

    public static function edit($type, $updates){
      return Authorization::update( $type , new User() , $updates );
    }

    static function login($username, $password){
      $result_salt = self::where("username", "=", $username)->first();
      $user = self:: where("username", "=", $username)
                    ->where("password", "=", sha1($result_salt->salt.$password))
                    ->first();
      if ($user) {
        $token=str_random(60);
        $user->api_token=$token;
        $user->expires_at=date('Y-m-d H:i:s', strtotime('+14 day', time()));
        $user->save();
        return $user->api_token;
      } else {
        return false;
      }
    }

    static function where_token($token){
      return self::where("api_token", "=", $token)->first();
    }

    static function list($number,$page){
      Paginator::currentPageResolver(function() use ($page) {
          return $page;
      });
      return self::show("*")->paginate($number);
    }

    static function get_($id){
      return self::show("*")->where("id", "=", $id)->first();
    }

    static function me(){
      return self::show("my")->first();
    }

    static function store($fields){
      return Authorization::store( "*" , new User() , $fields );
    }

    static function register($fields){
      return Authorization::store( "my" , new User() , $fields );
    }
}
