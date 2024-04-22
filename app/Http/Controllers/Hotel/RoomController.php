<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    // public function index(){
    //     $room_all = Room::orderBy('id','desc')->paginate(9);
    //     return view('hotel.roo',compact('room_all'));
    // }

    public function single_room($id){
        $single_room_data = Room::with('rRoomPhoto')->where('id',$id)->first();
       
       
        return view('hotel.room_detail', compact('single_room_data'));
    }
}
