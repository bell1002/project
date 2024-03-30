<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Hash;
use Auth;

class AdminProfileController extends Controller
{
    public function index(){
        return view('admin.profile');
    }

    public function profile_submit(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email',
        ]);

        // Fetch the admin data at the beginning
        $admin_data = Admin::where('email', Auth::guard('admin')->user()->email)->first();

        if (!$admin_data) {
            // Handle the case where admin is not found
            return redirect()->back()->withErrors('Admin not found.');
        }

        if ($request->password != '') {
            $request->validate([
                'password' => 'required',
                'retype_password' => 'required|same:password',
            ]);
            $admin_data->password = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpg,jpeg,png,gif',
            ]);
            // Assuming you handle the scenario where there's no existing photo gracefully
            if($admin_data->photo && file_exists(public_path('uploads/'.$admin_data->photo))){
                unlink(public_path('uploads/'.$admin_data->photo));
            }

            $ext = $request->file('photo')->extension();
            $final_name = 'admin_' . time() . '.' . $ext; // Added time() to make filename unique

            $request->file('photo')->move(public_path('uploads/'), $final_name);

            $admin_data->photo = $final_name;
        }

        // Update name and email
        $admin_data->name = $request->name;
        $admin_data->email = $request->email;

        // Save the updates
        $admin_data->save();

        return redirect()->back()->with('success', 'Profile information is saved successfully.');
    }
}
