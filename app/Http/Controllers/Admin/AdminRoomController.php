<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Amenity;
use App\Models\RoomPhoto;

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
        // Check if arr_amenities exists in the request and ensure it's an array
        $amenities = $request->has('arr_amenities') && is_array($request->arr_amenities) ? $request->arr_amenities : [];
    
        // Format amenities into a comma-separated string
        $formattedAmenities = implode(',', $amenities);
    
        $request->validate([
            'featured_photo'=> 'required|image|mimes:jpg,jpeg,png,gif'
        ]);
    
        $ext = $request->file('featured_photo')->extension();
        $final_name = time() . '.' . $ext;
        $request->file('featured_photo')->move(public_path('uploads/'), $final_name);
    
        $obj = new Room();
        $obj->featured_photo = $final_name;
        $obj->name = $request->name;
        $obj->description = $request->description;
        $obj->price = $request->price;
        $obj->total_rooms = $request->total_rooms;
        $obj->amenities = $formattedAmenities; // Save formatted amenities
        $obj->size = $request->size;
        $obj->total_beds = $request->total_beds;
        $obj->total_bathrooms = $request->total_bathrooms;
        $obj->total_balconies = $request->total_balconies;
        $obj->total_guests = $request->total_guests;
        $obj->video_id = $request->video_id;
    
        $obj->save();
    
        return redirect('/admin/room/view')->with('success', 'Room is added successfully.');
    }
    
    public function edit($id){
        $all_amenities = Amenity::get();
        $room_data = Room::where('id', $id)->first();

        $existing_amenities = explode(',', $room_data->amenities);
        return view('admin.room_edit', compact('room_data', 'all_amenities', 'existing_amenities'));
    }

    public function update(Request $request, $id){
        $obj = Room::findOrFail($id);

        if($request->hasFile('featured_photo')){
            $request->validate([
                'featured_photo'=> 'required|image|mimes:jpg,jpeg,png,gif'
            ]);

            unlink(public_path('uploads/' . $obj->featured_photo));
            $ext = $request->file('featured_photo')->extension();
            $final_name = time() . '.' . $ext;
            $request->file('featured_photo')->move(public_path('uploads/'), $final_name);

            $obj->featured_photo = $final_name;
        }

        // Format amenities into a comma-separated string
        $amenities = implode(',', $request->arr_amenities);
        $obj->name = $request->name;
        $obj->description = $request->description;
        $obj->price = $request->price;
        $obj->total_rooms = $request->total_rooms;
        $obj->amenities = $amenities; // Save formatted amenities
        $obj->size = $request->size;
        $obj->total_beds = $request->total_beds;
        $obj->total_bathrooms = $request->total_bathrooms;
        $obj->total_balconies = $request->total_balconies;
        $obj->total_guests = $request->total_guests;
        $obj->video_id = $request->video_id;

        $obj->save();

        return redirect('/admin/room/view')->with('success', 'Room is updated successfully.');
    }

    public function delete($id){
        $room = Room::findOrFail($id);
        unlink(public_path('uploads/' . $room->featured_photo));
        $room->delete();

        $room_photo_data = RoomPhoto::where('room_id',$id)->get();
        foreach($room_photo_data as $item){
            unlink(public_path('uploads/' . $item->photo));
            $item->delete();
        }
        return redirect('/admin/room/view')->with('success', 'Room is deleted successfully.');
    }

    public function gallery($id){
        $room_data = Room::where('id',$id)->first();
        $room_photos = RoomPhoto::where('room_id',$id)->get();

        return view('admin.room_gallery',compact('room_data','room_photos'));
    }

    public function gallery_store(Request $request,$id){

        $request->validate([
            'photo'=> 'required|image|mimes:jpg,jpeg,png,gif'
        ]);
    
        $ext = $request->file('photo')->extension();
        $final_name = time() . '.' . $ext;
        $request->file('photo')->move(public_path('uploads/'), $final_name);
    
        $obj = new RoomPhoto();
        $obj->photo = $final_name;
        $obj->room_id = $id;
        
        $obj->save();
    
        return redirect('/admin/room/view')->with('success', 'Room Photo is added successfully.');
    }

    public function gallery_delete($id){
        $single_data = RoomPhoto::where('id',$id)->first();
        unlink(public_path('uploads/'.$single_data->photo));
        $single_data->delete();

        return redirect('/admin/room/view')->with('success', 'Room photo is deleted successfully.');

    }
}
