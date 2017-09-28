<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your / screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function login(Request $request)
    {
        $credentials = $request->except('_token');
        if(Auth::attempt($credentials)){
            $user = Auth::user();
            if($user->is_active == true){
                $request->session()->flash('success','Logged in Successfully.');
                return redirect('/dashboard');
            }else{
                Auth::logout();
                $request->session()->flash('error','User is not activated yet. Please activate user first.');
                return redirect('/');
            }
        }
        return $this->sendFailedLoginResponse($request);
    }

    public function logout(\Illuminate\Http\Request $request){
        Auth::logout();
        $message="Logout Successful";
        $request->session()->flash('error', $message);
        return redirect('/');
    }

}
