<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Authorization;
use Auth;
/*
use DateTime;
use DB;
*/
class AuthorizationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    static function read(Request $request, $object){
      if (isset(Auth::user()->auth)) {
        $authorization = Authorization::select("read")
                              ->where("auth","=",0)
                              ->where("controller_actions","=",$request->route()[1]['uses'])
                              ->first();

        $read=array_filter(explode(",", $authorization->read));
        foreach ($object->getAttributes() as $key=>$property) {
          if (!in_array($key,$read)) {
            unset($object->$key);
          }
        }
        return $object;
      } else {
        return false;
      }
    }

    static function write(Request $request, $object){
      if (isset(Auth::user()->auth)) {
        $authorization = Authorization::select("write")
                              ->where("auth","=",0)
                              ->where("controller_actions","=",$request->route()[1]['uses'])
                              ->first();

        $write=array_filter(explode(",", $authorization->write));
        foreach ($object->getAttributes() as $key=>$property) {
          if (!in_array($key,$write)) {
            unset($object->$key);
          }
        }
        return $object;
      } else {
        return false;
      }
    }

}
