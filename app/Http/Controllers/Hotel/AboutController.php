<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class AboutController extends Controller
{
    public function index(){
        $about_data = Page::where('id',1)->first();
        return view('hotel.about',compact('about_data'));
    }
}
