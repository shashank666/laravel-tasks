<?php

namespace App\Http\Controllers\Admin\Auth;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    
    use AuthenticatesUsers;

   
    protected $redirectTo = '/cpanel';

    public function __construct()
    {
          $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm(){
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
      // Validate the form data
      $this->validate($request, [
        'email'   => 'required|email',
        'password' => 'required|min:6'
      ]);
    
      if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password,'status'=>1], $request->remember)) {
        // if successful, then redirect to their intended location
        return redirect()->intended(route('admin.dashboard'));
      }else{
        // if unsuccessful, then redirect back to the login with the form data
         return redirect()->back()->withInput($request->only('email', 'remember'))->with(['message'=>'Sorry, You are not authorized to login','alert-class'=>'alert-danger']);
      }
     
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect()->route('admin.login');
    }
    
}
