<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Authorization;
use Auth;
use Illuminate\Pagination\Paginator as Paginator;

function get_class_name($classname){
  if ($pos = strrpos(get_class($classname), '\\')) return substr(get_class($classname), $pos + 1);
  return $pos;
}

function get_join_name($join){
  return substr($join, strrpos($join, '\\') + 1);
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

    public function list(Request $request){
      $number=$request->has('request')?$request->number:15;
      $page=$request->has('page')?$request->page:1;
      Paginator::currentPageResolver(function() use ($page) {
          return $page;
      });
      return self::show("*", new Authorization())->paginate($number);
    }

    public function create(Request $request){
      if (self::store( "*" , new Authorization() , $request->all() ) ) {
        return "OK";
      } else {
        return "NO";
      }
    }

    public function delete($id){
      $auth = self::show("*", new Authorization())->where("id","=",$id);
      return self::destroy( $auth , new Authorization() );
    }

    public function edit(Request $request, $id){
      $auth = self::show("*", new Authorization())->where("id","=",$id);
      return self::update( $auth , new Authorization() , $request->all() );
    }

    private static function auth(){
      if (Auth::user()) {
        return Auth::user()->auth;
      } else {
        return 0;
      }
    }

    static function store($where , $object , $updates){
      $auth=self::auth();
      if (isset($auth)) {
        if ($where=='my') {
          $permission=1;
        } else if ($where=='*'){
          $permission=2;
        } else {
          throw new \RuntimeException('Only "me" or "*"');
        }

        $auth=Authorization::get_auth("store",$auth, $permission, get_class_name($object));
        $select=$auth['fields'];
        $own=$auth['own'];
        if (count($select)==0) {
          abort(403);
        }
        $new_object=$object;
        $check=0;
        foreach ($updates as $key => $value) {
          if ( ( in_array("*",$select) ) || in_array($key,$select)) {
            $new_object->{$key}=$value;
            $check++;
          }
        }
        if ($check!=0) {
          return $new_object->save();
        } else {
          return false;
        }
      }
    }

    static function update($where = null, $object, $updates){
      $auth=self::auth();
      if (isset($auth)) {
        $permission=0;
        if ($where=='my') {
          $permission=1;
          $objects = $object::where($own, "=", Auth::user()->id);
        } else if ($where=="*"){
          $permission=2;
          $objects = $object::whereRaw("1 = 1");
        } else if (is_object($where)){
          if (isset($where->permission)) {
            $permission = $where->permission;
          } else {
            $permission=0;
          }
          $objects = $where;
        } else {
          throw new \RuntimeException('Only "me", "*", or an object.');
        }
        $auth=Authorization::get_auth("update",$auth, $permission, get_class_name($object));
        $select=$auth['fields'];
        $own=$auth['own'];
        if (count($select)==0) {
          abort(403);
        }
        $real_updates=array();
        $real_fields = \Schema::getColumnListing($object->getTable());
        foreach ($updates as $key => $value) {
          if ( ( in_array("*",$select) ) || in_array($key,$select)) {
            if (in_array($key,$real_fields)) {
              $real_updates[$key]=$value;
            } else {
              return response('"'.get_class_name($object).'" has no "'.$key.'" property', 403);
            }
          }
        }
        $result = $objects->update($real_updates);
        if ($result) {
          return $result;
        } else {
          abort(403);
        }
      }
    }

    static function destroy($where = null, $object){
      $auth=self::auth();
      if (isset($auth)) {
        if (is_object($where)){
          if (isset($where->permission)) {
            $permission = $where->permission;
          } else {
            $permission=0;
          }
        } else {
          throw new \RuntimeException('Only object.');
        }
        $auth=Authorization::get_auth("destroy",$auth, $permission, get_class_name($object));
        $select=$auth['fields'];
        $own=$auth['own'];
        if (in_array("*",$select)) {
          return $where->delete();
        } else {
          abort(403);
        }
      }
    }

    static function show($where = null, $object){
      $auth=self::auth();
      if (isset($auth)) {
        if ($where=='my') {
          $permission=1;
        } else if ($where=='*'){
          $permission=2;
        } else {
          throw new \RuntimeException('Only "me" or "*"');
        }
        $auth=Authorization::get_auth("show",$auth, $permission, get_class_name($object));
        $select=$auth['fields'];
        $own=$auth['own'];
        if (count($select)==0) {
          abort(403);
        }
        $objects = $object::select($select);
        if ($where=='my') {
          if ($own!=null) {
            $objects->where($own, "=", Auth::user()->id);
          }
        }
        $objects->permission=$permission;
        return $objects;
      }
    }

    static function hasMany($where, $join, $foreign, $local){
      $auth=self::auth();
      if (isset($auth)) {
        if (is_object($where)){
          if (isset($where->permission)) {
            $permission = $where->permission;
          } else {
            $permission=0;
          }
        } else {
          throw new \RuntimeException('Only object.');
        }
        $auth=Authorization::get_auth("show",$auth, $permission, get_join_name($join));
        $select=$auth['fields'];
        $own=$auth['own'];
        if (count($select)==0) {
          abort(403);
        }
        $objects = $where->first()->hasMany($join,$foreign,$local)->select($select);
        if ($where->permission==1) {
          if ($own!=null) {
            $objects->where($own, "=", Auth::user()->id);
          }
        }
        $objects->permission=$permission;
        return $objects;
      }
    }

    static function belongsTo($where, $join, $foreign, $local){
      $auth=self::auth();
      if (isset($auth)) {
        if (is_object($where)){
          if (isset($where->permission)) {
            $permission = $where->permission;
          } else {
            $permission=0;
          }
        } else {
          throw new \RuntimeException('Only object.');
        }
        $auth=Authorization::get_auth("show",$auth, $permission, get_join_name($join));
        $select=$auth['fields'];
        $own=$auth['own'];
        if (count($select)==0) {
          abort(403);
        }
        $objects = $where->first()->belongsTo($join,$foreign,$local)->select($select);
        if ($where->permission==1) {
          if ($own!=null) {
            $objects->where($own, "=", Auth::user()->id);
          }
        }
        $objects->permission=$permission;
        return $objects;
      }
    }

    static function hasManyThrough($where, $join, $through, $foreign, $local){
      $auth=self::auth();
      if (isset($auth)) {
        if (is_object($where)){
          if (isset($where->permission)) {
            $permission = $where->permission;
          } else {
            $permission=0;
          }
        } else {
          throw new \RuntimeException('Only object.');
        }
        $auth=Authorization::get_auth("show",$auth, $permission, get_join_name($join));
        $select=$auth['fields'];
        $own=$auth['own'];
        if (count($select)==0) {
          abort(403);
        }
        $objects = $where->first()->hasManyThrough($join,$through,$foreign,$local)->select($select);
        if ($where->permission==1) {
          if ($own!=null) {
            $objects->where($own, "=", Auth::user()->id);
          }
        }
        $objects->permission=$permission;
        return $objects;
      }
    }
}
