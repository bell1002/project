<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slide;

class HomeController extends Controller
{
    public function index(){

        $slide_all = Slide::get();
        return view('hotel.home', compact('slide_all'));
    }
}
