<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Page;

class AdminPageController extends Controller
{
    public function about(){
        $page_data = Page::where('id',1)->first();
        return view('admin.page_about', compact('page_data'));
    }
    public function about_update(Request $request){
        $obj = Page::where('id',1)->first();
        if ($obj !== null) {
            // Assign values to $obj properties
            $obj->about_heading = $request->about_heading;
            $obj->about_content = $request->about_content;
        
            // Call the update method
            $obj->update();
        }
        

        return redirect()->back()->with('success', 'Faq is updated successfully.');

    }
}
