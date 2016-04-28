<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $salt;

    public function __construct()
    {
        $this->salt="changetheworld";
    }

    public function login(Request $request){
      if ($request->has('username') && $request->has('password') && $request->has('g-recaptcha-response')) {
        //check if captcha is ok
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query([
                    //TODO: change this key
                    'secret' => config('captcha.secret'),
                    'response' => $request->input('g-recaptcha-response')
                ])
            ]
        ]);
        $result = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context),true);
        if (!$result['success']) {
          return "CAPTCHA";
        }
        $user = User:: where("username", "=", $request->input('username'))
                      ->where("password", "=", sha1($this->salt.$request->input('password')))
                      ->first();
        if ($user) {
          $token=str_random(60);
          $user->api_token=$token;
          $user->save();
          return $user->api_token;
        } else {
          return "MISMATCH";
        }
      } else {
        return "INCOMPLETE";
      }
    }

    public function register(Request $request){
      if ($request->has('username') && $request->has('password') && $request->has('email')) {
        $user = new User;
        $user->username=$request->input('username');
        $user->password=sha1($this->salt.$request->input('password'));
        $user->email=$request->input('email');
        $user->confirmed=false;
        $user->api_token=str_random(60);
        if($user->save()){
          $url="http://".$_SERVER['SERVER_NAME']."/users/confirm/".$user->api_token;
          mail($request->input('email'), 'Attiva', "Attiva il tuo account premendo su questo link: <a href='".$url."'></a>");
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
      if ($user) {
        $user->confirmed=true;
        $user->api_token=str_random(60);
        $user->save();
        return "SUCCESS";
      } else {
        return "ERROR";
      }
    }

    public function me(){
      if (Gate::denies('authorization', [ class_basename($this), __FUNCTION__ ] )) {
          abort(403);
      }
      return Auth::user();
    }
}
