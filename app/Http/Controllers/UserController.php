<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Auth;
use DateTime;
use DB;
use App\Http\Controllers\AuthorizationController as Authorization;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function __construct()
    {
    }

    public function login(Request $request){
      if ($request->has('username') && $request->has('password')) {
        $token = User::login($request->username, $request->password);
        if ($token) {
          return $token;
        } else {
          return "MISMATCH";
        }
      } else {
        return "INCOMPLETE";
      }
    }

    private function sendRegistration($email,$api_token){
      $url="http://".$_SERVER['SERVER_NAME']."/users/confirm/".$api_token;
      mail($email, 'Attiva', "Attiva il tuo account premendo su questo link: <a href='".$url."'></a>");
    }

    public function register(Request $request){
      if ($request->has('username') && $request->has('password') && $request->has('email')) {
        $salt=str_random(16);
        $api_token=str_random(60);
        $fields = [
          "username"=>$request->input('username'),
          "password"=>sha1($salt.$request->input('password')),
          "salt"=>$salt,
          "email"=>$request->input('email'),
          "confirmed"=>false,
          "api_token"=>$api_token,
          "expires_at"=>date('Y-m-d H:i:s', strtotime('+14 day', time()))
        ];
        if(User::register($fields)){
          $this->sendRegistration($request->input('email'),$api_token);
          return "SUCCESS";
        } else {
          return "ERROR";
        }
      } else {
        return "INCOMPLETE";
      }
    }

    public function create(Request $request){
      if (User::store($request->all())) {
        return "OK";
      } else {
        return "NO";
      }
    }

    public function confirm($token){
      $user = User::where_token($token);
      $date1 = new DateTime($user->expires_at);
      $date2 = new DateTime("now");
      $api_token = str_random(60);
      $expires_at = date('Y-m-d H:i:s', strtotime('+14 day', time()));
      if ($date1<$date2) {
        $updates = [
          "api_token"=>$api_token,
          "expires_at"=>$expires_at
        ];
        User::edit($user, $updates);
        $this->sendRegistration($user->email,$api_token);
        return "SENT_MAIL";
      }
      if ($user) {
        $updates = [
          "api_token"=>$api_token,
          "expires_at"=>$expires_at,
          "confirmed"=>true
        ];
        if (User::edit($user, $updates)) {
          return "SUCCESS";
        }
      }
      return "ERROR";
    }

    public function list(Request $request){
      return User::list();
    }

    public function info(Request $request, $id){
      if ($id=="me") {
        $user = User::me();
      } else {
        $user = User::get_($id);
      }
      return $user;
    }

    public function edit(Request $request, $id){
      $updates=array();
      foreach ($request->all() as $key => $value) {
        $updates[$key]=$value;
      }
      $user = User::show("*")->where("id","=",$id);
      return User::edit($user, $updates);
    }
}
