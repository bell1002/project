<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Feature;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index(){

        $testimonial_all = Testimonial::get();
        $slide_all = Slide::get();
        $feature_all = Feature::get();
        return view('hotel.home', compact('slide_all','feature_all','testimonial_all'));
    }
}
