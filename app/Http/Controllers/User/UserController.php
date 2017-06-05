<?php

namespace App\Http\Controllers\User;

use App\Client;
use App\ClientUser;
use App\Role;
use App\User;
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
            return view('admin.user.create')->with(compact('roles'));
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

    public function createUser(Request $request){
        try{
            $data = $request->all();
            $data['first_name'] = ucfirst($data['first_name']);
            $data['last_name'] = ucfirst($data['last_name']);
            $data['password'] = bcrypt($data['password']);
            $data['is_active'] = (boolean)false;
            $user = User::create($data);
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

    public function getManageView(Request $request){
        try{
            return view('admin.user.manage');
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

    public function getClientView(Request $request){
        try{
            return view('admin.user.client.create');
        }catch(\Exception $e){
            $data = [
                'action' => 'View Client',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createClient(Request $request){
        try{
            $data = $request->all();
            $client = Client::create($data);
            $request->session()->flash('success', 'Client created successfully');
            return redirect('/client/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create new Client',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
