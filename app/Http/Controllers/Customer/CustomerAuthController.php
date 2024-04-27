<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Hash;
use Auth;

class CustomerAuthController extends Controller
{

    public function signup(){
        return view('hotel.signup');
    }
    public function login(){
        return view('hotel.login');
    }

    public function login_submit(Request $request){
       $request->validate([
        'email' => 'required|email',
        'password' => 'required'
       ]);

       $credentail=[
        'email'=> $request->email,
        'password'=> $request->password

       ];

       if(Auth::guard('customer')->attempt($credentail)){
        return redirect()->route('customer_home');

       }
       else{
        return redirect()->route('customer_login')->with('error', 'Information is not correct!');
       }
    }

    public function logout(){
        Auth::guard('customer')->logout();
        return redirect()->route('customer_login');
    }
}
