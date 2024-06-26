<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class BlogController extends Controller
{
    public function index(){
        $post_all = Post::orderBy('id','desc')->paginate(9);
        return view('hotel.blog',compact('post_all'));
    }

    public function single_post($id){
        $single_post_data = Post::where('id',$id)->first();
        $single_post_data->total_view = $single_post_data->total_view +1;
        $single_post_data->update();

        return view('hotel.post', compact('single_post_data'));
    }
}
