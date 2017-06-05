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
            $request->session()->flash('success', 'Client created successfully');
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
