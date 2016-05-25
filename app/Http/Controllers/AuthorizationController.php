<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Authorization;
use Auth;
/*
use DateTime;
use DB;
*/

function get_class_name($classname){
  if ($pos = strrpos(get_class($classname), '\\')) return substr(get_class($classname), $pos + 1);
  return $pos;
}

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

    static function store(){

    }

    static function update($where = null, $object, $updates){
      $auth=Auth::user()->auth;
      if (isset($auth)) {
        $permission=0;
        if ($where=='my') {
          $permission=1;
          $objects = $object::where($own, "=", Auth::user()->id);
        } else if ($where=="*"){
          $permission=2;
          $objects = $object::whereRaw("1 = 1");
        } else if (isset($where->permission)){
          $permission = $where->permission;
          $objects = $where;
        } else {
          return false;
        }

        $fields_own = Authorization::select("field","own")
                              ->where("auth", "=", $auth)
                              ->where("update", ">=", $permission)
                              ->where("update", "!=", 0)
                              ->where("object", "=", get_class_name($object));
        $select=$fields_own->pluck('field')->all();
        $own=$fields_own->pluck('own')->first();
        if (count($select)==0) {
          return $object;
        }
        $real_updates=array();
        foreach ($updates as $key => $value) {
          if (in_array($key,$select)) {
            $real_updates[$key]=$value;
          }
        }

        return $objects->update($real_updates);
      }
    }

    static function destroy(){

    }

    static function show($where = null, $object){
      $auth=Auth::user()->auth;
      if (isset($auth)) {
        $permission=0;
        if ($where=='*') {
          $permission=2;
        } else if ($where=='my'){
          $permission=1;
        }

        $fields_own = Authorization::select("field","own")
                              ->where("auth", "=", $auth)
                              ->where("show", ">=", $permission)
                              ->where("show", "!=", 0)
                              ->where("object", "=", get_class_name($object));
        $select=$fields_own->pluck('field')->all();
        if (count($select)==0) {
          return $object;
        }
        $objects = $object::select($select);
        if ($where=='my') {
          if ($fields_own->pluck('own')->first()!=null) {
            $objects->where($fields_own->pluck('own')->first(), "=", Auth::user()->id);
          }
        }
        $objects->permission=$permission;
        return $objects;
      }
    }
}
