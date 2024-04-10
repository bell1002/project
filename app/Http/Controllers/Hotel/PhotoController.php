<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Photo;

class PhotoController extends Controller
{
    public function index(){
        $photo_all = Photo::paginate(2);
        return view('hotel.photo_gallery',compact('photo_all'));
    }
}
