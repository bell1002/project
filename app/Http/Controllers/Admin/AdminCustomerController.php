<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;

class AdminCustomerController extends Controller
{
    public function index(){
        $customers = Customer::get();
        return view('admin.customer', compact('customers'));
    }
    public function change_status($id){
        $customer_data= Customer::where('id', $id)->first();
        if($customer_data->status == 1){
            $customer_data->status = 0;
        } else{
            $customer_data->status = 1;
        }

        $customer_data->update();
        return redirect()->back()->with('success', 'Status is changed successfully');
    }

    public function customer_status($status = null){

        if ($status == 'active') {
            $customers = Customer::where('status', 1)->get();
        } else {
            $customers = Customer::where('status', 0)->get();
        }

        $total_active_customers = Customer::where('status', 1)->count();
        $total_pending_customers = Customer::where('status', 0)->count();

        return view('admin.customer', compact('customers', 'total_active_customers','total_pending_customers'));
    }
}
