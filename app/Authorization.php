<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Authorization extends Model {
  protected $fillable = ['auth', 'controller_actions', 'read', 'write'];
}
