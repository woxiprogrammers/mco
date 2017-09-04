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
            return view('admin.role.edit')->with(compact('role'));
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
            dd($request->all());
            $webpermissions=$request->web_permissions;
            $mobilepermissions=$request->mobile_permissions;
            dd($mobilepermissions);
            $webpermissions->givePermissionTo('web_permissions');
            dd($webpermissions);
            $data = $request->only('name','type');
            $data['name'] = ucwords(trim($data['name']));

            $role = Role::create($data);
            /*
             * $webPermission & $mobile permission
             *
             * $rolepermisiondata = array()
             * $rolePermissionData[role_id] = $roleId
             *
             * foreach(webpermision){
             * $roleData[permision_id] = $permisonind
                $roledata[is_weeb] = true;
             *  if(role permision relation exists )
             *    // update query
             *   else
             *     // create query
             * }*/
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
            $role->update(['name' => ucwords(trim($request->name))]);
            $request->session()->flash('success', 'Role Edited successfully.');
            return redirect('/role/edit/'.$role->id);
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
            //dd($mobileModules->toArray());
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
            //dd($webModuleResponse);
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
            //dd($mobileModuleResponse);
            return view('partials.role.module-listing')->with(compact('webModuleResponse','permissionTypes','mobileModuleResponse'));
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