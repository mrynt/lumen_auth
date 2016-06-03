<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Authorization extends Model {
  public $timestamps = false;
  protected $fillable = ['auth', 'controller_actions', 'field', 'permissions'];

  public static function get_auth($type, $auth, $permission, $object){
    $fields_own = Authorization::select("field","own")
                          ->where("auth", "=", $auth)
                          ->where($type, ">=", $permission)
                          ->where($type, "!=", 0)
                          ->where("object", "=", $object);
    $data=array(
      "fields"=>$fields_own->pluck('field')->all(),
      "own"=>$fields_own->pluck('own')->first()
    );
    return $data;
  }

}
