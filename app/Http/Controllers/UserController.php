<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Illuminate\Support\Facades\Gate;
use DateTime;
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
        $user->password=sha1($this->salt.$request->input('password'));
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

    public function me(){
      if (Gate::denies('authorization', [ class_basename($this), __FUNCTION__ ] )) {
          abort(403);
      }
      return Auth::user();
    }
}
