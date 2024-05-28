<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Feature;
use App\Models\Testimonial;
use App\Models\Post;
use App\Models\Room;

class HomeController extends Controller
{
    public function index(){

        $testimonial_all = Testimonial::get();
        $slide_all = Slide::get();
        $feature_all = Feature::get();
        $post_all = Post::orderBy('id','desc')->limit(3)->get();
        $room_all = Room::get();

        return view('hotel.home', compact('slide_all','feature_all','testimonial_all','post_all','room_all'));
    }
    

}
