<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;

class VideoController extends Controller
{
    public function index(){
        $video_all = Video::paginate(1);
        return view('hotel.video_gallery',compact('video_all'));
    }
}
