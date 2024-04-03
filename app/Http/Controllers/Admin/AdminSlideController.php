<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slide;

class AdminSlideController extends Controller
{
    public function index(){
        $slides = Slide::get();
        return view('admin.slide_view', compact('slides'));
    }
}
