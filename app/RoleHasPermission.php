<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    protected $table = 'role_has_permissions';

    protected $fillable = ['role_id','permission_id','is_web','is_mobile'];

    protected function role(){
        return $this->belongsTo('App\Role','role_id');
    }

    protected function permission(){
        return $this->belongsTo('App\Permission','permission_id');
    }
}
