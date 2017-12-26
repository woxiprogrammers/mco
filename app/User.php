<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'email', 'password','last_name','is_active','mobile','dob','gender','purchase_order_amount_limit','purchase_peticash_amount_limit'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles(){
        return $this->hasMany('App\UserHasRole','user_id');
    }

    public function userProjectSitesRelation(){
        return $this->hasMany('App\UserProjectSiteRelation','user_id');
    }

    public function customHasPermission($permission){
        $permissionExists = UserHasPermission::join('permissions','permissions.id','=','user_has_permissions.permission_id')
                                            ->where('permissions.name','ilike',$permission)
                                            ->where('user_has_permissions.user_id',$this->id)
                                            ->where('user_has_permissions.is_web', true)
                                            ->first();
        if($permissionExists  == null){
            return false;
        }else{
            return true;
        }
    }
}
