<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Group;
use Auth;
use App\Http\Controllers\AuthorizationController as Authorization;

class GroupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function __construct()
    {
    }

    public function listUsers($description){
      if (!isset($description)) {
        return "bad";
      }
      $group=new Group();
      return $group->users($description);
    }

    public function listAuths($description){
      if (!isset($description)) {
        return "bad";
      }
      $group=new Group();
      return $group->auths($description);
    }
/*
    public function create(Request $request){
      if (User::store($request->all())) {
        return "OK";
      } else {
        return "NO";
      }
    }

    public function edit(Request $request, $id){
      $updates=array();
      foreach ($request->all() as $key => $value) {
        $updates[$key]=$value;
      }
      $user = User::show("*")->where("id","=",$id);
      return User::edit($user, $updates);
    }

    public function delete($id){
      $user = User::show("*")->where("id","=",$id);
      return Authorization::destroy( $user , new User() );
    }
*/
}
