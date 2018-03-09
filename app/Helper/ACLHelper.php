<?php
/**
 * Created by Ameya Joshi.
 * Date: 23/8/17
 * Time: 2:16 PM
 */

namespace App\Helper;

use App\Module;
use App\PermissionType;
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
                    ->get()
                    ->toArray();
            }else{
                $userPermissionCount = UserHasPermission::join('permissions','user_has_permissions.permission_id','=','permissions.id')
                    ->join('modules','modules.id','=','permissions.module_id')
                    ->where('user_has_permissions.user_id', $user->id)
                    ->where('modules.id', $moduleId)
                    ->select('user_has_permissions.permission_id as permission_id','modules.name as module_name')
                    ->get()
                    ->toArray();
            }
            if(count($userPermissionCount) > 0){
                return true;
            }else{
                return false;
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Check module\'s ACL',
                'slug' => $moduleSlug,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public static function getPermissions($moduleIds){
        try{

            $webModules = Module::join('permissions','modules.id','=','permissions.module_id')
                ->whereIn('modules.module_id',$moduleIds)
                ->where('permissions.is_web',true)
                ->select('modules.name as module_name','permissions.name as permission_name','modules.id as submodule_id','modules.module_id as module_id','permissions.type_id as permission_type_id','permissions.id as permission_id')
                ->orderBy('submodule_id')
                ->get();
            $mobileModules =  Module::join('permissions','modules.id','=','permissions.module_id')
                ->whereIn('modules.module_id',$moduleIds)
                ->where('permissions.is_mobile',true)
                ->select('modules.name as module_name','permissions.name as permission_name','modules.id as submodule_id','modules.module_id as module_id','permissions.type_id as permission_type_id','permissions.id as permission_id')
                ->orderBy('submodule_id')
                ->get();
            $webModuleResponse = array();
            foreach ($webModules as $subModule){
                if($subModule['module_id'] == null){
                    $subModule['module_id'] = $subModule['submodule_id'];
                }
                if(!array_key_exists($subModule['module_id'],$webModuleResponse)){
                    $webModuleResponse[$subModule['module_id']] = array();
                    $webModuleResponse[$subModule['module_id']]['id'] = $subModule['module_id'];
                    $webModuleResponse[$subModule['module_id']]['module_name'] = Module::where('id', $subModule['module_id'])->pluck('name')->first();
                    $webModuleResponse[$subModule['module_id']]['submodules'] = array();
                }
                if(!array_key_exists($subModule['submodule_id'],$webModuleResponse[$subModule['module_id']]['submodules'])){
                    $webModuleResponse[$subModule['module_id']]['submodules'][$subModule['submodule_id']]['id'] = $subModule['submodule_id'];
                    $webModuleResponse[$subModule['module_id']]['submodules'][$subModule['submodule_id']]['module_id'] = $subModule['module_id'];
                    $webModuleResponse[$subModule['module_id']]['submodules'][$subModule['submodule_id']]['submodule_name'] = $subModule['module_name'];
                    $webModuleResponse[$subModule['module_id']]['submodules'][$subModule['submodule_id']]['permissions'] = array();
                }
                $webModuleResponse[$subModule['module_id']]['submodules'][$subModule['submodule_id']]['permissions'][$subModule['permission_type_id']] = $subModule['permission_id'];
            }

            $mobileModuleResponse = array();
            foreach ($mobileModules as $subModule){
                if($subModule['module_id'] == null){
                    $subModule['module_id'] = $subModule['submodule_id'];
                }
                if(!array_key_exists($subModule['module_id'],$mobileModuleResponse)){
                    $mobileModuleResponse[$subModule['module_id']] = array();
                    $mobileModuleResponse[$subModule['module_id']]['id'] = $subModule['module_id'];
                    $mobileModuleResponse[$subModule['module_id']]['module_name'] = Module::where('id', $subModule['module_id'])->pluck('name')->first();
                    $mobileModuleResponse[$subModule['module_id']]['submodules'] = array();
                }
                if(!array_key_exists($subModule['submodule_id'],$mobileModuleResponse[$subModule['module_id']]['submodules'])){
                    $mobileModuleResponse[$subModule['module_id']]['submodules'][$subModule['submodule_id']]['id'] = $subModule['submodule_id'];
                    $mobileModuleResponse[$subModule['module_id']]['submodules'][$subModule['submodule_id']]['module_id'] = $subModule['module_id'];
                    $mobileModuleResponse[$subModule['module_id']]['submodules'][$subModule['submodule_id']]['submodule_name'] = $subModule['module_name'];
                    $mobileModuleResponse[$subModule['module_id']]['submodules'][$subModule['submodule_id']]['permissions'] = array();
                }
                $mobileModuleResponse[$subModule['module_id']]['submodules'][$subModule['submodule_id']]['permissions'][$subModule['permission_type_id']] = $subModule['permission_id'];
            }
            $permissionTypes = PermissionType::select('id','name')->get()->toArray();
            $data = array();
            $data['webModuleResponse'] = $webModuleResponse;
            $data['permissionTypes'] = $permissionTypes;
            $data['mobileModuleResponse'] = $mobileModuleResponse;
            return $data;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Permissions',
                'modules' => $moduleIds,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}