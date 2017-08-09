<?php

namespace App\Http\Controllers\User;

use App\Client;
use App\ClientUser;
use App\Http\Requests\UserRequest;
use App\Role;
use App\User;
use App\UserHasRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function getUserView(Request $request){
        try{
            $roles = Role::get()->toArray();
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
            $request->session()->flash('success', 'User created successfully');
            return redirect('/user/create');
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

    public function getEditView(Request $request,$user){
        try{
            $roles = Role::get()->toArray();
            $user = $user->toArray();
            return view('user.edit')->with(compact('user','roles'));
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
            $data = $request->except('role_id');
            $user->update($data);
            $userRoleData = array();
            $userRoleData['role_id'] = $request->
            $request->session()->flash('success', 'User Edited successfully.');
            return redirect('/user/edit/'.$user->id);
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
            $userData = User::orderBy('id','asc')->get()->toArray();
            $iTotalRecords = count($userData);
            $records = array();
            $iterator = 0;
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($userData); $iterator++,$pagination++ ){
                if($userData[$pagination]['is_active'] == true){
                    $user_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $user_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    $userData[$pagination]['first_name'].' '.$userData[$pagination]['last_name'] ,
                    $userData[$pagination]['email'],
                    $userData[$pagination]['mobile'],
                    $user_status,
                    date('d M Y',strtotime($userData[$pagination]['created_at'])),
                    '<div class="btn-group">
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
                                    <i class="icon-tag"></i> '.$status.' </a>
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

}
