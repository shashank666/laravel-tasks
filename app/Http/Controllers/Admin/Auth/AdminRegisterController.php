<?php

namespace App\Http\Controllers\Admin\Auth;
use Illuminate\Support\Str;

use App\Model\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminRegisterController extends Controller
{

     use RegistersUsers;
    protected $redirectTo = '/cpanel';

    
    public function __construct()
    {
        $this->middleware('guest:admin');
    }

    public function showRegisterForm()
    {
        return view('admin.auth.register');
    }

    public function store(Request $request){
      if($request->securekey=="4Y&8K@jnCuN*g1t"){
          $this->validate($request,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $request['password'] = bcrypt($request->password);
        $user = Admin::create($request->all());
        Auth::login($user);
        return redirect(route('admin.login'))->with(['message'=>'Successfully registered, Kindly login with your registered email address','alert-class'=>'alert-success']);; 
    }
    else
        return redirect(route('admin.register'))->with(['message'=>'Sorry, You are not authorized to signup here','alert-class'=>'alert-danger']);
    }

}
