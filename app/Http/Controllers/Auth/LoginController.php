<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
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

    public function authenticate(Request $reqeust){
      try{
        dd($request->all());
        $user = User::where('email', $request->email)->first();
            if ($user == NULL || empty($user)){
              $message="The email address is invalid";
              $request->session()->flash('error', $message);
              return back()->withInput();
            }else{
              $userRole = Role::where('slug','admin')->first();
              if (Auth::attempt(['email' => $request->email,'password' => $request->password, 'role_id'=>$userRole->id])) {
                  $roleType = Role::findOrFail(Auth::user()->role_id);
                  $request->session()->put('role_type', $roleType->slug);
                  return redirect('dashboard');
              } else{
                  $message="The email address or password is invalid";
                  $request->session()->flash('error', $message);
                  return back()->withInput();
              }
            }
      }catch(\Exception $e){
        $data = [
          'action' => "Authenticate",
          'data' => $request->all(),
          'exception' => $e->getMessage()
        ];
        Log::critical(json_encode($data));
        abort(500);
      }
    }
}
