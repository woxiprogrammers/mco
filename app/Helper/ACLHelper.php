<?php
/**
 * Created by Ameya Joshi.
 * Date: 23/8/17
 * Time: 2:16 PM
 */

namespace App\Helper;

use App\Module;
use App\UserHasPermission;
use Illuminate\Support\Facades\Auth;

class ACLHelper{

    public static function checkModuleAcl($moduleSlug){
        try{
            $user = Auth::user();
            $moduleId = Module::where('slug',$moduleSlug)->pluck('id')->first();
            $subModuleCount = Module::where('module_id', $moduleId)->count();
            if($subModuleCount > 0){
                $userPermissionCount = UserHasPermission::join('permissions','user_has_permissions.permission_id','=','permissions.id')
                    ->join('modules','modules.id','=','permissions.module_id')
                    ->where('user_has_permissions.user_id', $user->id)
                    ->where('modules.module_id', $moduleId)
                    ->select('user_has_permissions.permission_id as permission_id','modules.name as module_name')
                    ->get()->toArray();
            }else{
                $userPermissionCount = UserHasPermission::join('permissions','user_has_permissions.permission_id','=','permissions.id')
                    ->join('modules','modules.id','=','permissions.module_id')
                    ->where('user_has_permissions.user_id', $user->id)
                    ->where('modules.id', $moduleId)
                    ->select('user_has_permissions.permission_id as permission_id','modules.name as module_name')
                    ->get()->toArray();
            }
            if($userPermissionCount > 0){
                return true;
            }else{
                return false;
            }
        }catch (\Exception $e){

        }
    }
}