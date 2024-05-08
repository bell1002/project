<?php

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\BookedRoom;
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
    
    public function predict_price($room_id, $checkin_date, $checkout_date, $adults, $children) {
        // Lệnh gọi script Python với các giá trị đối số từ dữ liệu nhập từ màn hình
        $command = "python predict_price.py $room_id \"$checkin_date\" \"$checkout_date\" $adults $children";
    
        // Thực hiện lệnh và nhận kết quả từ script Python
        $predictedPrice = exec($command);
    
        return $predictedPrice;
    }
    
    
    public function cart_submit(Request $request)
    {
        $request->validate([
            'room_id' => 'required',
            'checkin_checkout' => 'required',
            'adult' => 'required'
        ]);

        $dates = explode(' - ',$request->checkin_checkout);
        $checkin_date = $dates[0];
        $checkout_date = $dates[1];

        // Lấy dữ liệu từ form
    $room_id = $request->room_id;
    $adults = $request->adult;
    $children = $request->children;

    // Gọi hàm predict_price để dự đoán giá phòng
    $price = $this->predict_price($room_id, $checkin_date, $checkout_date, $adults, $children);

    // Kiểm tra dự đoán giá phòng
    if ($price === null) {
        return redirect()->back()->with('error', 'Failed to predict room price.');
    }
    
    dd('Predicted room price: ' . $price);
    // Lưu thông tin vào session
    session()->push('cart_room_id', $room_id);
    session()->push('cart_checkin_date', $checkin_date);
    session()->push('cart_checkout_date', $checkout_date);
    session()->push('cart_adult', $adults);
    session()->push('cart_children', $children);
    session()->push('cart_price', $price);
    // Sử dụng giá phòng dự đoán trong ứng dụng của bạn
   // echo "Predicted room price: $price";
        return redirect()->back()->with('success', 'Room is added to the cart successfully.');
    }

    public function cart_view()
    {
        return view('Hotel.cart');
    }
  


}
