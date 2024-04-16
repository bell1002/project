<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\Websitemail;
use App\Models\Subscriber;

class SubscriberController extends Controller
{
    public function send_email(Request $request){
        $validator = \Validator::make($request->all(),[
            'email'=>'required|email'
        ]);
        if(!$validator->passes()){
            return response()->json(['code'=>0,'error_message'=>$validator->errors()->toArray()]);

        }
        else{
            $token = hash('sha256',time());

            $obj = new Subcsriber();
            $obj->email = $request->email;
            $obj->token = token;
            $obj->status =0;
            $obj->save() ;

            $verification_link = url('/subscriber/verify/'.$request->email.'/'.$token);
            //send email
            $subject = 'Contact form email';
            $message = 'Please click on the link below to confirm subcsrition: <br>';
            $message .= '<a href="'.$verification_link.'">';
            $message .= $verification_link;
            $message .= '</a>';

            $admin_data = Admin::where('id', 1)->first();
            $admin_data = $admin_data->email;

            \Mail::to($admin_data)->send(new Websitemail($subject, $message));
            return response()->json(['code'=>1, 'success_message'=>'Please check your email to confirm subscrition']);

        }
    
    }

    public function verify(){
        
    }
}
