<?php
/**
 * Created by Ameya Joshi
 * Date: 23/5/17
 */

namespace App\Http\Controllers\CustomTraits;
use App\Helper\ACLHelper;
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
            $modules = Module::whereNull('module_id')->get();
            $subModuleIds = RoleHasPermission::join('permissions','permissions.id','=','role_has_permissions.permission_id')
                        ->join('modules','modules.id','=','permissions.module_id')
                        ->where('role_has_permissions.role_id',$role['id'])
                        ->orderBy('modules.id','asc')
                        ->select('modules.id as module_id')
                        ->distinct()
                        ->get()->toArray();
            if(count($subModuleIds) > 0){
                $subModuleIds = array_column($subModuleIds,'module_id');
                $moduleIds = Module::whereIn('id',$subModuleIds)->select('module_id')->distinct()->get()->toArray();
                $moduleIds = array_column($moduleIds,'module_id');
                $data = ACLHelper::getPermissions($moduleIds);
                $roleWebPermissions = RoleHasPermission::where('role_id',$role['id'])->where('is_web', true)->pluck('permission_id')->toArray();
                $roleMobilePermissions = RoleHasPermission::where('role_id',$role['id'])->where('is_mobile', true)->pluck('permission_id')->toArray();
                $webModuleResponse = $data['webModuleResponse'];
                $permissionTypes = $data['permissionTypes'];
                $mobileModuleResponse = $data['mobileModuleResponse'];
                $showAclTable = true;
            }else{
                $moduleIds = array();
                $webModuleResponse = array();
                $mobileModuleResponse = array();
                $permissionTypes = array();
                $roleMobilePermissions = array();
                $roleWebPermissions = array();
                $showAclTable = false;
            }
            return view('admin.role.edit')->with(compact('role','modules','moduleIds','webModuleResponse','permissionTypes','roleWebPermissions','roleMobilePermissions','mobileModuleResponse','showAclTable'));
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
            if($request->web_permissions != null) {
                foreach ($web_permissions as $permissions) {
                    $rolePermissionData['is_web'] = true;
                    $rolePermissionData['permission_id'] = $permissions;
                    $check = RoleHasPermission::where('role_id')->where('permission_id')->first();
                    if ($check != null) {
                        RoleHasPermission::where('role_id', $roleId)->where('permission_id',$permissions)->update(['is_web' => true]);
                    } else {
                        RoleHasPermission::create($rolePermissionData);
                    }
                }
            }
            $rolePermissionData = array();
            $rolePermissionData['role_id'] = $roleId;
            if($request->mobile_permissions != null) {
                foreach ($mobile_permissions as $permissions) {
                    $rolePermissionData['is_mobile'] = true;
                    $rolePermissionData['permission_id'] = $permissions;
                    $check = RoleHasPermission::where('role_id')->where('permission_id')->first();
                    if ($check != null) {
                        RoleHasPermission::where('role_id', $roleId)->where('permission_id',$permissions)->update(['is_mobile' => true]);
                    } else {
                        RoleHasPermission::create($rolePermissionData);
                    }
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
                    $check = RoleHasPermission::where('role_id',$roleId)->where('permission_id',$permissions)->first();
                    if ($check != null) {
                        RoleHasPermission::where('role_id', $roleId)->where('permission_id',$permissions)->update($rolePermissionData);
                    }else{
                        $rolePermissionData['permission_id'] = $permissions;
                        RoleHasPermission::create($rolePermissionData);
                    }
                }
                $webPermissions = $request->web_permissions;
            }else{
                $webPermissions = array();
            }
            $rolePermissionData = array();
            $rolePermissionData['role_id'] = $roleId;
            if($request->mobile_permissions != null ) {
                $mobilePermissions = $request->mobile_permissions;
                foreach ($request->mobile_permissions as $permissions) {
                    $rolePermissionData['is_mobile'] = true;
                    $check = RoleHasPermission::where('role_id',$roleId)->where('permission_id',$permissions)->first();
                    if ($check != null) {
                        RoleHasPermission::where('role_id', $roleId)->where('permission_id',$permissions)->update(['is_mobile'=> true]);
                    } else {
                        $rolePermissionData['permission_id'] = $permissions;
                        RoleHasPermission::create($rolePermissionData);
                    }
                }
            }else{
                $mobilePermissions = array();
            }
            $deletedIds = RoleHasPermission::where('role_id',$roleId)->whereNotIn('permission_id',$webPermissions)->whereNotIn('permission_id',$mobilePermissions)->delete();
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
                $rolesData = Role::where('name','ilike','%'.$request->search_name.'%')->whereNotIn('slug',['admin','superadmin'])->orderBy('name','asc')->get()->toArray();
            }else{
                $rolesData = Role::whereNotIn('slug',['admin','superadmin'])->orderBy('name','asc')->get()->toArray();
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
            $data = ACLHelper::getPermissions($moduleIds);
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
}