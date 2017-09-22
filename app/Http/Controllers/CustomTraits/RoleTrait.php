<?php
/**
 * Created by Ameya Joshi
 * Date: 23/5/17
 */

namespace App\Http\Controllers\CustomTraits;
use App\Module;
use App\PermissionType;
use App\Role;
use App\Http\Requests\RoleRequest;
use App\RoleHasPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Array_;

trait RoleTrait{

    public function getCreateView(Request $request){
        try{
            $modules = Module::whereNull('module_id')->get();
            return view('admin.role.create')->with(compact('modules'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get role create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request,$role){
        try{
            $role = $role->toArray();
            $modules = Module::whereNull('module_id')->get();
            $subModuleIds = RoleHasPermission::join('permissions','permissions.id','=','role_has_permissions.permission_id')
                        ->join('modules','modules.id','=','permissions.module_id')
                        ->where('role_has_permissions.role_id',$role['id'])
                        ->orderBy('modules.id','asc')
                        ->select('modules.id as module_id')
                        ->distinct()
                        ->get()->toArray();
            $subModuleIds = array_column($subModuleIds,'module_id');
            $moduleIds = Module::whereIn('id',$subModuleIds)->select('module_id')->distinct()->get()->toArray();
            $moduleIds = array_column($moduleIds,'module_id');
            $data = $this->getPermissions($moduleIds);
            $roleWebPermissions = RoleHasPermission::where('role_id',$role['id'])->where('is_web', true)->pluck('permission_id')->toArray();
            $roleMobilePermissions = RoleHasPermission::where('role_id',$role['id'])->where('is_mobile', true)->pluck('permission_id')->toArray();
            $webModuleResponse = $data['webModuleResponse'];
            $permissionTypes = $data['permissionTypes'];
            $mobileModuleResponse = $data['mobileModuleResponse'];
            return view('admin.role.edit')->with(compact('role','modules','moduleIds','webModuleResponse','permissionTypes','roleWebPermissions','roleMobilePermissions','mobileModuleResponse'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get role edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

        public function getManageView(Request $request){
        try{
            return view('admin.role.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get role manage view',
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createRole(RoleRequest $request){
        try{
            $web_permissions=$request->web_permissions;
            $mobile_permissions=$request->mobile_permissions;
            $data = $request->only('name','type');
            $data['name'] = ucwords(trim($data['name']));
            $role = Role::create($data);
            $roleId = $role['id'];
            $rolePermissionData = array();
            $rolePermissionData['role_id'] = $roleId;

            foreach ($web_permissions as $permissions)
            {
                $rolePermissionData['is_web'] = true;
                $rolePermissionData['permission_id'] = $permissions;
                $check = RoleHasPermission::where('role_id')->where('permission_id')->first();
                if($check != null)
                {
                        RoleHasPermission::where('role_id',$roleId)->update('is_web',true);
                }
                else{
                        RoleHasPermission::create($rolePermissionData);
                }
            }

            foreach ($mobile_permissions as $permissions)
            {
                $rolePermissionData['is_mobile'] = true;
                $rolePermissionData['permission_id'] = $permissions;
                $check = RoleHasPermission::where('role_id')->where('permission_id')->first();
                if($check != null)
                {
                    RoleHasPermission::where('role_id',$roleId)->update('is_mobile',true);
                }
                else{
                    RoleHasPermission::create($rolePermissionData);
                }
            }

            $request->session()->flash('success', 'Role Created successfully.');
            return redirect('/role/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Role',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editRole(RoleRequest $request, $role){
        try{
            $roleId = $role->id;
            $data = $request->only('name','type');
            $data['name'] = ucwords(trim($data['name']));
            $role->update($data);
            $rolePermissionData = array();
            $rolePermissionData['role_id'] = $roleId;
            if($request->web_permissions != null) {
                foreach ($request->web_permissions as $permissions) {
                    $rolePermissionData['is_web'] = true;
                    $rolePermissionData['is_mobile'] = false;
                    $check = RoleHasPermission::where('role_id')->where('permission_id')->first();
                    if ($check != null) {
                        RoleHasPermission::where('role_id', $roleId)->where('permission_id',$permissions)->update($rolePermissionData);
                    }else{
                        $rolePermissionData['permission_id'] = $permissions;
                        RoleHasPermission::create($rolePermissionData);
                    }
                }
            }
            if($request->mobile_permissions != null ) {
                foreach ($request->mobile_permissions as $permissions) {
                    $rolePermissionData['is_mobile'] = true;
                    $check = RoleHasPermission::where('role_id')->where('permission_id')->first();
                    if ($check != null) {
                        RoleHasPermission::where('role_id', $roleId)->where('permission_id',$permissions)->update('is_mobile', true);
                    } else {
                        $rolePermissionData['permission_id'] = $permissions;
                        RoleHasPermission::create($rolePermissionData);
                    }
                }
            }
            $deletedIds = RoleHasPermission::where('role_id',$roleId)->whereNotIn('permission_id',$request->web_permissions)->whereNotIn('permission_id',$request->mobile_permissions)->delete();
            $request->session()->flash('success', 'Role Edited successfully.');
            return redirect('/role/edit/'.$role->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit Role',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function roleListing(Request $request){
        try{
            if($request->has('search_name')){
                $rolesData = Role::where('name','ilike','%'.$request->search_name.'%')->orderBy('name','asc')->get()->toArray();
            }else{
                $rolesData = Role::orderBy('name','asc')->get()->toArray();
            }
            $iTotalRecords = count($rolesData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($rolesData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($rolesData); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $rolesData[$pagination]['name'],
                    ucfirst($rolesData[$pagination]['type']),
                    date('d M Y',strtotime($rolesData[$pagination]['created_at'])),
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/role/edit/'.$rolesData[$pagination]['id'].'">
                                <i class="icon-docs"></i> Edit </a>
                            </li>
                        </ul>
                    </div>'
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Role Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
    }

    public function checkRoleName(Request $request){
        try{
            $roleName = $request->name;
            if($request->has('role_id')){
                $nameCount = Role::where('name','ilike',$roleName)->where('id','!=',$request->role_id)->count();
            }else{
                $nameCount = Role::where('name','ilike',$roleName)->count();
            }
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Role name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getSubModules(Request $request){
        try{

            $moduleIds = $request->module_id;
            $data = $this->getPermissions($moduleIds);
            $webModuleResponse = $data['webModuleResponse'];
            $permissionTypes = $data['permissionTypes'];
            $mobileModuleResponse = $data['mobileModuleResponse'];
            if($request->has('role_id')){
                $role = $request->role_id;
                $roleWebPermissions = RoleHasPermission::where('role_id',$role)->where('is_web', true)->pluck('permission_id')->toArray();
                $roleMobilePermissions = RoleHasPermission::where('role_id',$role)->where('is_mobile', true)->pluck('permission_id')->toArray();

            }else{
                $roleWebPermissions = [];
                $roleMobilePermissions = [];
            }
            return view('partials.role.module-listing')->with(compact('moduleIds','webModuleResponse','permissionTypes','mobileModuleResponse','roleWebPermissions','roleMobilePermissions'));
        }
        catch (\Exception $e){
            $data = [
                'action' => 'Get Submodules',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }


    }

    public function getPermissions($moduleIds){
        try{
            $webModules = Module::join('permissions','modules.id','=','permissions.module_id')
                ->whereIn('modules.module_id',$moduleIds)
                ->where('permissions.is_web',true)
                ->select('modules.name as module_name','permissions.name as permission_name','modules.id as submodule_id','modules.module_id as module_id','permissions.type_id as permission_type_id','permissions.id as permission_id')
                ->get();

            $mobileModules =  Module::join('permissions','modules.id','=','permissions.module_id')
                ->whereIn('modules.module_id',$moduleIds)
                ->where('permissions.is_mobile',true)
                ->select('modules.name as module_name','permissions.name as permission_name','modules.id as submodule_id','modules.module_id as module_id','permissions.type_id as permission_type_id','permissions.id as permission_id')
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