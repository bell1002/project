<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\BookedRoom;
use App\Models\Amenity;
use App\Models\Room;
use Auth;
use DB;
use App\Mail\Websitemail;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
Use Stripe;

class BookingController extends Controller
{
    public function predict_price($data_json)
{
    // Chuyển đổi dữ liệu JSON thành mảng
    $data = json_decode($data_json, true);

    // Lấy room_id từ dữ liệu
    $room_id = $data['room_id'];

    // Thực hiện truy vấn cơ sở dữ liệu để lấy thông tin phòng từ room_id
    $room = Room::find($room_id);

    if ($room) {
        // Lấy name từ room
        $name = $room->name;

        // Khởi tạo mảng để lưu trữ tên amenities
        $amenities_names = [];

        // Lấy danh sách mã amenities từ phòng
        $amenities_ids = explode(',', $room->amenities);

        // Lặp qua từng mã amenities để lấy tên tương ứng
        foreach ($amenities_ids as $amenity_id) {
            // Thực hiện truy vấn cơ sở dữ liệu để lấy tên amenities từ bảng amenities
            $amenity = Amenity::find($amenity_id);
            if ($amenity) {
                // Nếu tìm thấy, thêm tên amenities vào mảng
                $amenities_names[] = $amenity->name;
            } 
        }

        // Thêm name và amenities vào dữ liệu đầu vào
        $data['name'] = $name;
        $data['amenities'] = $amenities_names;
    } else {
        // Xử lý trường hợp không tìm thấy thông tin phòng
        // ...

        // Nếu không tìm thấy, trả về lỗi
        return response()->json(['error' => 'Room not found'], 404);
    }

    // Chuyển đổi lại dữ liệu thành JSON
    $data_json = json_encode($data);
    var_dump($data_json);
    // Set up cURL để thực hiện yêu cầu POST
    $url = 'http://127.0.0.1:5000/predict';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_json)
    ));

    // Thực hiện yêu cầu POST và nhận kết quả
    $result = curl_exec($ch);

    // Đóng phiên cURL
    curl_close($ch);

    // Trả kết quả về
    return $result;
}



    public function cart_submit(Request $request)
    {
        $request->validate([
            'room_id' => 'required',
            'checkin_checkout' => 'required',
            'adult' => 'required'
        ]);

        $dates = explode(' - ', $request->checkin_checkout);
        $checkin_date = $dates[0];
        $checkout_date = $dates[1];

        // Get data from the form
        $room_id = $request->room_id;
        $adults = $request->adult;
        $children = $request->children;

        // Prepare data for prediction
        $data = [
            'room_id' => $room_id,
            'checkin_date' => $checkin_date,
            'checkout_date' => $checkout_date,
            'adults' => $adults,
            'children' => $children
        ];

        // Convert data to JSON
        $data_json = json_encode($data);

        // Call the predict_price function to predict the room price
        $price = $this->predict_price($data_json);

        // Check if room price prediction is successful
        if ($price === null) {
            return redirect()->back()->with('error', 'Failed to predict room price.');
        }

        // Debugging purpose - Display predicted room price
        //dd( 'Predicted room price: ' . $price);

        // Save information in session
        session()->push('cart_room_id', $room_id);
        session()->push('cart_checkin_date', $checkin_date);
        session()->push('cart_checkout_date', $checkout_date);
        session()->push('cart_adult', $adults);
        session()->push('cart_children', $children);
        session()->push('cart_price', $price);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Room is added to the cart successfully.');
    }

    public function cart_view()
    {
        return view('hotel.cart');
    }

    public function cart_delete($id)
    {
        $arr_cart_room_id = array();
        $i=0;
        foreach(session()->get('cart_room_id') as $value) {
            $arr_cart_room_id[$i] = $value;
            $i++;
        }

        $arr_cart_checkin_date = array();
        $i=0;
        foreach(session()->get('cart_checkin_date') as $value) {
            $arr_cart_checkin_date[$i] = $value;
            $i++;
        }

        $arr_cart_checkout_date = array();
        $i=0;
        foreach(session()->get('cart_checkout_date') as $value) {
            $arr_cart_checkout_date[$i] = $value;
            $i++;
        }

        $arr_cart_adult = array();
        $i=0;
        foreach(session()->get('cart_adult') as $value) {
            $arr_cart_adult[$i] = $value;
            $i++;
        }

        $arr_cart_children = array();
        $i=0;
        foreach(session()->get('cart_children') as $value) {
            $arr_cart_children[$i] = $value;
            $i++;
        }

        session()->forget('cart_room_id');
        session()->forget('cart_checkin_date');
        session()->forget('cart_checkout_date');
        session()->forget('cart_adult');
        session()->forget('cart_children');

        for($i=0;$i<count($arr_cart_room_id);$i++)
        {
            if($arr_cart_room_id[$i] == $id) 
            {
                continue;    
            }
            else
            {
                session()->push('cart_room_id',$arr_cart_room_id[$i]);
                session()->push('cart_checkin_date',$arr_cart_checkin_date[$i]);
                session()->push('cart_checkout_date',$arr_cart_checkout_date[$i]);
                session()->push('cart_adult',$arr_cart_adult[$i]);
                session()->push('cart_children',$arr_cart_children[$i]);
            }
        }

        return redirect()->back()->with('success', 'Cart item is deleted.');

    }

    public function checkout()
    {
        if(!Auth::guard('customer')->check()) {
            return redirect()->back()->with('error', 'You must have to login in order to checkout');
        }

        if(!session()->has('cart_room_id')) {
            return redirect()->back()->with('error', 'There is no item in the cart');
        }

        return view('hotel.checkout');
    }
    
    public function payment(Request $request)
    {
        if(!Auth::guard('customer')->check()) {
            return redirect()->back()->with('error', 'You must have to login in order to checkout');
        }

        if(!session()->has('cart_room_id')) {
            return redirect()->back()->with('error', 'There is no item in the cart');
        }

        $request->validate([
            'billing_name' => 'required',
            'billing_email' => 'required|email',
            'billing_phone' => 'required',
            'billing_country' => 'required',
            'billing_address' => 'required',
            'billing_state' => 'required',
            'billing_city' => 'required',
            'billing_zip' => 'required'
        ]);

        session()->put('billing_name',$request->billing_name);
        session()->put('billing_email',$request->billing_email);
        session()->put('billing_phone',$request->billing_phone);
        session()->put('billing_country',$request->billing_country);
        session()->put('billing_address',$request->billing_address);
        session()->put('billing_state',$request->billing_state);
        session()->put('billing_city',$request->billing_city);
        session()->put('billing_zip',$request->billing_zip);

        return view('hotel.payment');
    }
}
