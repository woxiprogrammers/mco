<?php

namespace App\Http\Controllers\User;

use App\Client;
use App\ClientUser;
use App\Helper\ACLHelper;
use App\Http\Requests\UserRequest;
use App\Module;
use App\Permission;
use App\ProjectSite;
use App\Role;
use App\RoleHasPermission;
use App\User;
use App\UserHasPermission;
use App\UserHasRole;
use App\UserProjectSiteRelation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function getUserView(Request $request){
        try{
            $roles = Role::whereNotIn('slug',['admin','superadmin'])->get()->toArray();
            return view('user.create')->with(compact('roles'));
        }catch(\Exception $e){
            $data = [
                'action' => 'View User',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createUser(UserRequest $request){
        try{
            $userExists = User::where('mobile',$request->mobile)->first();
            if($userExists == null){
                $data = $request->except('role_id');
                $data['first_name'] = ucfirst($data['first_name']);
                $data['last_name'] = ucfirst($data['last_name']);
                $data['password'] = bcrypt($data['password']);
                $data['is_active'] = (boolean)false;
                $user = User::create($data);
                $userHasRoleData = array();
                $userHasRoleData['role_id'] = $request->role_id;
                $userHasRoleData['user_id'] = $user->id;
                UserHasRole::create($userHasRoleData);
                if($request->has('web_permissions')){
                    $userPermissionData = array();
                    $userPermissionData['user_id'] = $user->id;
                    $web_permissions=$request->web_permissions;
                    foreach ($web_permissions as $permissions){
                        $userPermissionData['is_web'] = true;
                        $userPermissionData['permission_id'] = $permissions;
                        $check = UserHasPermission::where('user_id',$user->id)->where('permission_id',$permissions)->first();
                        if($check != null){
                            UserHasPermission::where('user_id',$user->id)->where('permission_id',$permissions)->update(['is_web'=>true]);
                        }
                        else{
                            UserHasPermission::create($userPermissionData);
                        }
                    }
                }
                if($request->has('mobile_permissions')){
                    $userPermissionData = array();
                    $userPermissionData['user_id'] = $user->id;
                    $mobile_permissions=$request->mobile_permissions;
                    foreach ($mobile_permissions as $permissions){
                        $userPermissionData['is_mobile'] = true;
                        $userPermissionData['permission_id'] = $permissions;
                        $check = UserHasPermission::where('user_id',$user->id)->where('permission_id',$permissions)->first();
                        if($check != null){
                            UserHasPermission::where('user_id',$user->id)->update(['is_mobile' => true]);
                        }
                        else{
                            UserHasPermission::create($userPermissionData);
                        }
                    }
                }
                $request->session()->flash('success', 'User created successfully');
            }else{
                $request->session()->flash('error', 'Mobile number is already registered with other user.');
            }
            return redirect('/user/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create new User',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request,$userEdit){
        try{
            $subModuleIds = UserHasPermission::join('permissions','permissions.id','=','user_has_permissions.permission_id')
                ->join('modules','modules.id','=','permissions.module_id')
                ->where('user_has_permissions.user_id',$userEdit->id)
                ->orderBy('modules.id','asc')
                ->select('modules.id as module_id')
                ->distinct()
                ->get()->toArray();
            if(count($subModuleIds) > 0){
                $subModuleIds = array_column($subModuleIds,'module_id');
                $moduleIds = Module::whereIn('id',$subModuleIds)->select('module_id')->distinct()->get()->toArray();
                $moduleIds = array_column($moduleIds,'module_id');
                $data = ACLHelper::getPermissions($moduleIds);
                $userWebPermissions = UserHasPermission::where('user_id',$userEdit->id)->where('is_web', true)->pluck('permission_id')->toArray();
                $userMobilePermissions = UserHasPermission::where('user_id',$userEdit->id)->where('is_mobile', true)->pluck('permission_id')->toArray();
                $webModuleResponse = $data['webModuleResponse'];
                $permissionTypes = $data['permissionTypes'];
                $mobileModuleResponse = $data['mobileModuleResponse'];
                $showAclTable = true;
            }else{
                $moduleIds = array();
                $webModuleResponse = array();
                $mobileModuleResponse = array();
                $permissionTypes = array();
                $userMobilePermissions = array();
                $userWebPermissions = array();
                $showAclTable = false;
            }
            $projectSites = UserProjectSiteRelation::join('project_sites','project_sites.id','=','user_project_site_relation.project_site_id')
                                        ->join('projects','projects.id','=','project_sites.project_id')
                                        ->join('clients','clients.id','=','projects.client_id')
                                        ->where('user_project_site_relation.user_id',$userEdit->id)
                                        ->select('project_sites.id as project_site_id','project_sites.address as address','project_sites.name as project_site_name','projects.name as project_name','clients.company as client_company')
                                        ->get();
            if(count($projectSites) > 0){
                $showSiteTable = true;
                $projectSites = $projectSites->toArray();
            }else{
                $showSiteTable = false;
                $projectSites = array();
            }
            $purchaseOrderCreatePermission = Permission::join('user_has_permissions','permissions.id','=','user_has_permissions.permission_id')
                ->where('permissions.name','create-peticash-management')->where('permissions.is_mobile',true)
                ->where('user_has_permissions.user_id',$userEdit->id)->count();
            $peticashManagementPermission = Permission::join('user_has_permissions','permissions.id','=','user_has_permissions.permission_id')
                ->where('permissions.name','create-peticash-management')->where('permissions.is_mobile',true)
                ->where('user_has_permissions.user_id',$userEdit->id)->count();
            return view('user.edit')->with(compact('userEdit','roles','userWebPermissions','userMobilePermissions','webModuleResponse','mobileModuleResponse','showAclTable','permissionTypes','projectSites','showSiteTable','purchaseOrderCreatePermission','peticashManagementPermission'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get user edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editUser(UserRequest $request, $user){
        try{
            $data = $request->except('role_id','web_permissions','mobile_permissions');
            $user->update($data);
            if($request->has('web_permissions')){
                $userPermissionData = array();
                $userPermissionData['user_id'] = $user->id;
                $web_permissions = $request->web_permissions;
                foreach ($web_permissions as $permissions){
                    $userPermissionData['is_web'] = true;
                    $userPermissionData['is_mobile'] = false;
                    $userPermissionData['permission_id'] = $permissions;
                    $check = UserHasPermission::where('user_id',$user->id)->where('permission_id',$permissions)->first();
                    if($check != null){
                        UserHasPermission::where('user_id',$user->id)->where('permission_id',$permissions)->update(['is_web'=>true]);
                    }else{
                        UserHasPermission::create($userPermissionData);
                    }
                }
                UserHasPermission::where('user_id',$user->id)->whereNotIn('permission_id',$web_permissions)->update(['is_web' => false]);
            }else{
                $web_permissions = array();
            }
            if($request->has('mobile_permissions')){
                $userPermissionData = array();
                $userPermissionData['user_id'] = $user->id;
                $mobile_permissions = $request->mobile_permissions;
                foreach ($mobile_permissions as $permissions){
                    $userPermissionData['is_mobile'] = true;
                    $userPermissionData['permission_id'] = $permissions;
                    $check = UserHasPermission::where('user_id',$user->id)->where('permission_id',$permissions)->first();
                    if($check != null){
                        UserHasPermission::where('user_id',$user->id)->where('permission_id',$permissions)->update(['is_mobile' => true]);
                    }
                    else{
                        UserHasPermission::create($userPermissionData);
                    }
                }
                UserHasPermission::where('user_id',$user->id)->whereNotIn('permission_id',$mobile_permissions)->update(['is_mobile' => false]);
            }else{
                $mobile_permissions = array();
            }
            $deletedPermissions = UserHasPermission::where('user_id',$user->id)->whereNotIn('permission_id',$web_permissions)->whereNotIn('permission_id',$mobile_permissions)->get();
            foreach($deletedPermissions as $deletedPermission){
                $deletedPermission->delete();
            }
            $request->session()->flash('success', 'User Edited successfully.');
            return redirect('/user/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit User',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request){
        try{
            return view('user.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get User manage view',
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function userListing(Request $request){
        try{
            $user = Auth::user();
            $userData = User::join('user_has_roles','user_has_roles.user_id','=','users.id')
                            ->join('roles','roles.id','=','user_has_roles.role_id')
                            ->whereNotIn('roles.slug',['admin','superadmin'])
                            ->orderBy('users.id','asc')
                            ->select('users.id as id','users.first_name as first_name','users.last_name as last_name','users.email as email','users.mobile as mobile','users.created_at as created_at','users.is_active as is_active')
                            ->get();

            $iTotalRecords = count($userData);
            $records = array();
            if(count($userData) > 0){
                $userData = $userData->toArray();
            }else{
                $userData = array();
            }
            $records['data'] = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($userData); $iterator++,$pagination++ ){
                if($userData[$pagination]['is_active'] == true){
                    $user_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $user_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-manage-user')){
                    $actionButton = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                        <li>
                                <a href="/user/edit/'.$userData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/user/change-status/'.$userData[$pagination]['id'].'">
                                    <i class="icon-tag"></i> Approve / Disapprove </a>
                            </li>
                        </ul>
                    </div>';
                }else{
                    $actionButton = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                        <li>
                                <a href="/user/edit/'.$userData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                        </ul>
                    </div>';
                }
                $records['data'][$iterator] = [
                    $userData[$pagination]['first_name'].' '.$userData[$pagination]['last_name'] ,
                    $userData[$pagination]['email'],
                    $userData[$pagination]['mobile'],
                    $user_status,
                    date('d M Y',strtotime($userData[$pagination]['created_at'])),
                    $actionButton
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'User listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

        return response()->json($records,200);
    }

    public function changeUserStatus(Request $request, $user){
        try{
            $newStatus = (boolean)!$user['is_active'];
            $user->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'User Status changed successfully.');
            return redirect('/user/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change user status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getRoleAcls(Request $request, $roleId){
        try{
            $user = Auth::user();
            $userRole = $user->roles[0]->role->slug;
            $subModuleIds = RoleHasPermission::join('permissions','permissions.id','=','role_has_permissions.permission_id')
                ->join('modules','modules.id','=','permissions.module_id')
                ->where('role_has_permissions.role_id',$roleId)
                ->orderBy('modules.id','asc')
                ->select('modules.id as module_id')
                ->distinct()
                ->get()->toArray();
            $subModuleIds = array_column($subModuleIds,'module_id');
            $moduleIds = Module::whereIn('id',$subModuleIds)->select('module_id')->distinct()->get()->toArray();
            $moduleIds = array_column($moduleIds,'module_id');
            $data = ACLHelper::getPermissions($moduleIds);
            $roleWebPermissions = RoleHasPermission::where('role_id',$roleId)->where('is_web', true)->pluck('permission_id')->toArray();
            $roleMobilePermissions = RoleHasPermission::where('role_id',$roleId)->where('is_mobile', true)->pluck('permission_id')->toArray();
            $webModuleResponse = $data['webModuleResponse'];
            $permissionTypes = $data['permissionTypes'];
            $mobileModuleResponse = $data['mobileModuleResponse'];
            return view('partials.role.module-listing')->with(compact('roleWebPermissions','roleMobilePermissions','permissionTypes','webModuleResponse','mobileModuleResponse','userRole'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Role Acls',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function checkMobile(Request $request){
        try{
            if($request->has('user_id')){
                $nameCount = User::where('mobile',$request->mobile)->where('id','!=',$request->user_id)->count();
            }else{
                $nameCount = User::where('mobile',$request->mobile)->count();
            }
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch (\Exception $e){
            $data = [
                'action' => 'Check user mobile',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return null;
        }
    }

    public function projectSiteAutoSuggest(Request $request,$keyword){
        try{
            $projectSites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                                    ->join('clients','clients.id','=','projects.client_id')
                                    ->where('projects.name','ilike','%'.$keyword.'%')
                                    ->select('project_sites.id as project_site_id','project_sites.address as address','project_sites.name as project_site_name','projects.name as project_name','clients.company as client_company')
                                    ->get();
            $response = array();
            if(count($projectSites) > 0){
                $response = $projectSites->toArray();
                $iterator = 0;
                foreach($response as $projectData){
                    $response[$iterator]['tr_view'] = '<input name="project_sites[]" type="hidden" value="'.$projectData['project_site_id'].'">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">
                                                                    <b>Client</b>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="control-label">
                                                                    '.$projectData['client_company'].'
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">
                                                                    <b>Project</b>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <label class="control-label">
                                                                    '.$projectData['project_name'].'
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">
                                                                    <b>Project Site</b>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <label class="control-label">
                                                                    '.$projectData['project_site_name'].'
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="control-label pull-right">
                                                                    <b>Project Site Address</b>
                                                                </label>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <label class="control-label">
                                                                    '.$projectData['address'].'
                                                                </label>
                                                            </div>
                                                        </div>';
                    $iterator++;
                }
            }

            $status = 200;
        }catch (\Exception $e){
            $status = 500;
            $response = array();
            $data = [
                'action' => 'User Project Site',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
        return response()->json($response,$status);
    }

    public function assignProjectSites(Request $request,$user){
        try{
            if($request->has('project_sites')){
                $userProjectSiteData = array();
                $userProjectSiteData['user_id'] = $user->id;
                foreach($request->project_sites as $projectSiteID){
                    $check = UserProjectSiteRelation::where('user_id',$user->id)->where('project_site_id',$projectSiteID)->first();
                    if($check == null){
                        $userProjectSiteData['project_site_id'] = $projectSiteID;
                        UserProjectSiteRelation::create($userProjectSiteData);
                    }
                }
                $userSites = UserProjectSiteRelation::where('user_id',$user->id)->whereNotIn('project_site_id',$request->project_sites)->get();
                foreach ($userSites as $site){
                    $site->delete();
                }
            }else{
                $userSites = UserProjectSiteRelation::where('user_id',$user->id)->get();
                foreach ($userSites as $site){
                    $site->delete();
                }
            }
            $request->session()->flash('success', 'Project sites assigned to users successfully.');
            return redirect('/user/edit/'.$user->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Assign User Project Site',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getPermission(Request $request){
        try{
            $peticashManagementPermission = Permission::join('role_has_permissions','permissions.id','=','role_has_permissions.permission_id')
                ->where('permissions.name','create-peticash-management')->where('permissions.is_mobile',true)
                ->where('role_has_permissions.role_id',$request['role_id'])->count();
            if($peticashManagementPermission > 0){
                $data['peticash_management_permission'] = true;
            }else{
                $data['peticash_management_permission'] = false;
            }
            $purchaseCreatePermission = Permission::join('role_has_permissions','permissions.id','=','role_has_permissions.permission_id')
                ->where('permissions.name','create-purchase-order')
                ->where('role_has_permissions.role_id',$request['role_id'])->count();
            if($purchaseCreatePermission > 0){
                $data['purchase_create_permission'] = true;
            }else{
                $data['purchase_create_permission'] = false;
            }
            return view('/partials/user/user-create')->with(compact('data'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get Permissions",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
        }
        Log::critical(json_encode($data));
        abort(500);
    }

    public function checkEmail(Request $request){
        try{
            if($request->has('user_id')){
                $emailCount = User::where('email','ilike',$request->email)->where('id','!=',$request->user_id)->count();
            }else{
                $emailCount = User::where('email','ilike',$request->email)->count();
            }
            if($emailCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'User Check Email',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return null;
        }
    }

    public function getChangePasswordView(Request $request){
        try{
            return view('change-password');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get change password view',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function changePassword(Request $request){
        try{
            $user = Auth::user();

            if (Hash::check($request['old_password'], $user['password'])) {
                $user->update([
                    'password' => bcrypt($request['confirm_password'])
                ]);
                $message = "Password changed successfully";
                Auth::logout();
                $request->session()->flash('success', $message);
                return redirect('/');
            }else{
                $message = "Old Password doesn't match";
                return Redirect::back()->with('error', $message);
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Change Password',
                'exception' => $e->getMessage(),
                'params' => $request->all()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
