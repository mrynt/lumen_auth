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
        $result_salt = DB::  table('users')
                      ->select('salt')
                      ->where("username", "=", $request->input('username'))
                      ->first();
        $user = User:: where("username", "=", $request->input('username'))
                      ->where("password", "=", sha1($result_salt->salt.$request->input('password')))
                      ->first();
        if ($user) {
          $token=str_random(60);
          $user->api_token=$token;
          $user->expires_at=date('Y-m-d H:i:s', strtotime('+14 day', time()));
          $user->save();
          return $user->api_token;
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
        $user = new User;
        $user->username=$request->input('username');
        $salt=str_random(16);
        $user->salt=$salt;
        $user->password=sha1($salt.$request->input('password'));
        $user->email=$request->input('email');
        $user->confirmed=false;
        $user->api_token=str_random(60);
        $user->expires_at=date('Y-m-d H:i:s', strtotime('+14 day', time()));
        if($user->save()){
          $this->sendRegistration($request->input('email'),$user->api_token);
          return "SUCCESS";
        } else {
          return "ERROR";
        }
      } else {
        return "INCOMPLETE";
      }
    }

    public function confirm($token){
      $user = User:: where("api_token", "=", $token)
                    ->first();
      $date1 = new DateTime($user->expires_at);
      $date2 = new DateTime("now");
      if ($date1<$date2) {
        $user->api_token=str_random(60);
        $user->expires_at=date('Y-m-d H:i:s', strtotime('+14 day', time()));
        $user->save();
        $this->sendRegistration($user->email,$user->api_token);
        return "SENT_MAIL";
      }
      if ($user) {
        $user->confirmed=true;
        $user->api_token=str_random(60);
        $user->expires_at=date('Y-m-d H:i:s', strtotime('+14 day', time()));
        $user->save();
        return "SUCCESS";
      } else {
        return "ERROR";
      }
    }

    private function get_user($request,$id){
      $user=null;
      if ($id=="me") {
        $user=Auth::user();
      } else {
        $user = User::where("id", "=", $id)
                      ->first();
        if (!$user) {
          return false;
        }
        $user=Authorization::read($request, $user);
      }
      return $user;
    }

    public function info(Request $request, $id){
      $user=$this->get_user($request,$id);
      if ($user) {
        return $user;
      } else {
        return "No user found";
      }
    }

    public function edit(Request $request, $id){
      $user=$this->get_user($request,$id);
      if ($user) {
        foreach ($request->all() as $key => $value) {
          $user->$key=$value;
        }
        $user=Authorization::write($request, $user);
        $user->save();
        return $this->get_user($request,$id);
      } else {
        return "No user found";
      }
    }
}
