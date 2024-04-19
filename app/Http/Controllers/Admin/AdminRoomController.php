<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomPhoto;
use App\Models\Amenity;


class AdminRoomController extends Controller
{

    public function index(){
        $rooms = Room::get();
        return view('admin.room_view', compact('rooms'));
    }

    public function add(){
        $all_amenities = Amenity::get();
        return view('admin.room_add', compact('all_amenities'));
    }

    public function store(Request $request){

        $amenities = '';
        $i = 0;
        if(isset($request->arr_amenities)){
            foreach($request->arr_amenities as $item){
                if($i = 0){
                    $amenities .= $item;
                }
                else{
                    $amenities .= ','.$item;
                }
                $i++;
            }
        }
        $request->validate([
            'featured_photo'=> 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        $ext= $request->file('featured_photo')->extension();
        $final_name= time().'.'.$ext;
        $request->file('featured_photo')->move(public_path('uploads/'),$final_name);

        $obj = new Room();
        $obj->featured_photo = $final_name;
        $obj->name = $request->name;
        $obj->description = $request->description;
        $obj->price = $request->price;
        $obj->total_rooms = $request->total_rooms;
        $obj->amenities = $request->amenities;
        $obj->size = $request->size;
        $obj->total_beds = $request->total_beds;
        $obj->total_bathrooms = $request->total_bathrooms;
        $obj->total_balconies = $request->total_balconies;
        $obj->total_guests = $request->total_guests;
        $obj->video_id = $request->video_id;

        $obj->save();

        return redirect('/admin/room/view')->with('success', 'Slide is added successfully.');
    }
}
