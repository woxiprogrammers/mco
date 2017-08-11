<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = ['name','module_id','is_web','is_mobile','type'];

    public function roles(){
        return $this->hasMany('App\RoleHasPermission','permission_id');
    }
}
