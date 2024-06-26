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
use GuzzleHttp\Client;

class BookingController extends Controller
{
//     public function predict_price($data_json)
// {
//     // Chuyển đổi dữ liệu JSON thành mảng
//      $data = json_decode($data_json, true);

//     // // Lấy room_id từ dữ liệu
//     // $room_id = $data['room_id'];

//     // // Thực hiện truy vấn cơ sở dữ liệu để lấy thông tin phòng từ room_id
//     // $room = Room::find($room_id);

//     // if ($room) {
//     //     // Lấy name từ room
//     //     $name = $room->name;

//     //     // Khởi tạo mảng để lưu trữ tên amenities
//     //     $amenities_names = [];

//     //     // Lấy danh sách mã amenities từ phòng
//     //     $amenities_ids = explode(',', $room->amenities);

//     //     // Lặp qua từng mã amenities để lấy tên tương ứng
//     //     foreach ($amenities_ids as $amenity_id) {
//     //         // Thực hiện truy vấn cơ sở dữ liệu để lấy tên amenities từ bảng amenities
//     //         $amenity = Amenity::find($amenity_id);
//     //         if ($amenity) {
//     //             // Nếu tìm thấy, thêm tên amenities vào mảng
//     //             $amenities_names[] = $amenity->name;
//     //         } 
//     //     }

//     //     // Thêm name và amenities vào dữ liệu đầu vào
//     //     $data['name'] = $name;
//     //     $data['amenities'] = $amenities_names;
//     // } else {
//     //     // Xử lý trường hợp không tìm thấy thông tin phòng
//     //     // ...

//     //     // Nếu không tìm thấy, trả về lỗi
//     //     return response()->json(['error' => 'Room not found'], 404);
//     // }

//     //Chuyển đổi lại dữ liệu thành JSON
//     $data_json = json_encode($data);
   
//     // Set up cURL để thực hiện yêu cầu POST
//     $url = 'http://127.0.0.1:5000/predict';
//     $ch = curl_init($url);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//         'Content-Type: application/json',
//         'Content-Length: ' . strlen($data_json)
//     ));

//     // Thực hiện yêu cầu POST và nhận kết quả
//     $result = curl_exec($ch);

//     // Đóng phiên cURL
//     curl_close($ch);

//     // Trả kết quả về
//     return $result;
//     // Dữ liệu đầu vào

//     // $data_string = json_encode($data);
//     // // Gửi yêu cầu POST đến Flask API
//     // $url = 'http://127.0.0.1:5000/predict';
//     // $options = array(
//     //     'http' => array(
//     //         'header'  => "Content-type: application/json\r\n",
//     //         'method'  => 'POST',
//     //         'content' => $data_string,
//     //     ),
//     // );
//     // $context  = stream_context_create($options);
//     // $result = file_get_contents($url, false, $context);

//     // if ($result === FALSE) {
//     //     die('Error occurred');
//     // }

//     // // Hiển thị kết quả dự đoán
//     // $prediction = json_decode($result, true);
//     // echo "Prediction: " . print_r($prediction, true);
  

// }
    public function predict_price($data_json){
        // Create a GuzzleHTTP client
        $client = new Client();
        try {
            // Send a POST request to the Flask API
            $response = $client->post('http://127.0.0.1:5000/predict', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $data_json
            ]);
            // Get the response body
            $body = $response->getBody();
            $result = json_decode($body, true);
            // Return the predicted price
            return $result['prediction'] ?? null;
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to predict room price: ' . $e->getMessage());
            // Return null if an error occurs
            return null;
        }
    }

    public function cart_submit(Request $request)
    {
       
      
    // Lấy ngày hôm nay
    $selected_date = date('d/m/Y');
    
    // Lấy phòng từ request
    $room_id = $request->input('room_id');
   
    // Lấy số lượng phòng có sẵn cho phòng đã chọn
    $available_rooms = Room::find($room_id)->total_rooms - BookedRoom::where('room_id', $room_id)->where('booking_date', $selected_date)->count();
    
    // Kiểm tra số lượng phòng có sẵn
    if ($available_rooms <= 0) {
        return redirect()->back()->with('error', 'The selected room is not available.');
    }
    


        // Validate dữ liệu từ form
        $request->validate([
            'room_id' => 'required',
            'checkin_checkout' => 'required',
            'adult' => 'required'
        ]);
    
        // Tách ngày checkin và checkout từ chuỗi nhập vào
        $dates = explode(' - ', $request->checkin_checkout);
        $checkin_date = $dates[0];
        $checkout_date = $dates[1];
    
        // Lấy dữ liệu từ form
        $room_id = $request->room_id;
        $adults = $request->adult;
        $children = $request->children;
    
        // Chuẩn bị dữ liệu cho dự đoán giá phòng
        $data = [
            'features' => [
                'room_id' => (int)$room_id,
                'checkin_date' => (int)strtotime($checkin_date),
                'checkout_date' => (int)strtotime($checkout_date),
                'adults' => (int)$adults,
                'children' => (int)$children
            ]
        ];
    
        // Chuyển dữ liệu thành JSON
        $data_json = json_encode($data);
    
        // Gọi hàm predict_price để dự đoán giá phòng
        $price = $this->predict_price($data_json);
    
        // Kiểm tra xem dự đoán giá phòng có thành công không
        if ($price === null) {
            return redirect()->back()->with('error', 'Failed to predict room price.');
        }
    
        // Lưu thông tin vào session
        session()->push('cart_room_id', $room_id);
        session()->push('cart_checkin_date', $checkin_date);
        session()->push('cart_checkout_date', $checkout_date);
        session()->push('cart_adult', $adults);
        session()->push('cart_children', $children);
        session()->push('cart_price', $price);
    
        // Chuyển hướng trở lại trang trước với thông báo thành công
        return redirect()->back()->with('success', 'Room is added to the cart successfully.');
    }
    
    public function cart_view()
    {
        return view('hotel.cart');
    }

    public function cart_delete($id)
    {
        // Initialize arrays to hold the cart data
        $arr_cart_room_id = session()->get('cart_room_id', []);
        $arr_cart_checkin_date = session()->get('cart_checkin_date', []);
        $arr_cart_checkout_date = session()->get('cart_checkout_date', []);
        $arr_cart_adult = session()->get('cart_adult', []);
        $arr_cart_children = session()->get('cart_children', []);
        $arr_cart_price = session()->get('cart_price', []);
    
        // Clear the current session data
        session()->forget('cart_room_id');
        session()->forget('cart_checkin_date');
        session()->forget('cart_checkout_date');
        session()->forget('cart_adult');
        session()->forget('cart_children');
        session()->forget('cart_price');
    
        // Iterate through the cart items and re-add them to the session if they don't match the ID to delete
        for ($i = 0; $i < count($arr_cart_room_id); $i++) {
            if ($arr_cart_room_id[$i] == $id) {
                continue;
            } else {
                session()->push('cart_room_id', $arr_cart_room_id[$i]);
                session()->push('cart_checkin_date', $arr_cart_checkin_date[$i]);
                session()->push('cart_checkout_date', $arr_cart_checkout_date[$i]);
                session()->push('cart_adult', $arr_cart_adult[$i]);
                session()->push('cart_children', $arr_cart_children[$i]);
                session()->push('cart_price', $arr_cart_price[$i]);
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

    public function paypal($final_price)
    {
        $client = 'AVkdBUPPKSP31YyPAyWhfO5yc3RD_ivEy95vVFXnAhzstkm4N8312YHNZH19-uC75ZxmTZscoS9_b7N_';
        $secret = 'EAfclTA_1S0u95j2EWUQ30l8qJ68lhERBbJOyUKeuwIFBOs59UZoxRZlJofjuRqDBqGxcqSNYha2gHPh';

        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $client, // ClientID
                $secret // ClientSecret
            )
        );

        $paymentId = request('paymentId');
        $payment = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId(request('PayerID'));

        $transaction = new Transaction();
        $amount = new Amount();
        $details = new Details();

        $details->setShipping(0)
            ->setTax(0)
            ->setSubtotal($final_price);

        $amount->setCurrency('USD');
        $amount->setTotal($final_price);
        $amount->setDetails($details);
        $transaction->setAmount($amount);
        $execution->addTransaction($transaction);
        $result = $payment->execute($execution, $apiContext);

        if($result->state == 'approved')
        {
            $paid_amount = $result->transactions[0]->amount->total;
            
            $order_no = time();

            $statement = DB::select("SHOW TABLE STATUS LIKE 'orders'");
            $ai_id = $statement[0]->Auto_increment;

            $obj = new Order();
            $obj->customer_id = Auth::guard('customer')->user()->id;
            $obj->order_no = $order_no;
            $obj->transaction_id = $result->id;
            $obj->payment_method = 'PayPal';
            $obj->paid_amount = number_format($final_price, 2);
            $obj->booking_date = date('d/m/Y');
            $obj->status = 'Completed';
            $obj->save();
            
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

            $arr_cart_price = session()->get('cart_price');

            for($i=0;$i<count($arr_cart_room_id);$i++)
            {
                $r_info = Room::where('id',$arr_cart_room_id[$i])->first();
                $d1 = explode('/',$arr_cart_checkin_date[$i]);
                $d2 = explode('/',$arr_cart_checkout_date[$i]);
                $d1_new = $d1[2].'-'.$d1[1].'-'.$d1[0];
                $d2_new = $d2[2].'-'.$d2[1].'-'.$d2[0];
                $t1 = strtotime($d1_new);
                $t2 = strtotime($d2_new);
                // $diff = ($t2-$t1)/60/60/24;
                // $sub = $r_info->price;
                $room_price = $arr_cart_price[$i];

                // Kiểm tra nếu giá phòng không phải là số thực
                if (!is_float($room_price)) {
                    // Nếu không phải số thực, thử lấy phần tử đầu tiên trong mảng
                    $room_price = is_array($room_price) ? $room_price[0] : $room_price;
                }

                // Kiểm tra lại nếu giá phòng là số thực sau khi xử lý
                if (is_float($room_price)) {
                    // Tính toán tổng giá trị
                    $sub = $room_price;
                }
                $obj = new OrderDetail();
                $obj->order_id = $ai_id;
                $obj->room_id = $arr_cart_room_id[$i];
                $obj->order_no = $order_no;
                $obj->checkin_date = $arr_cart_checkin_date[$i];
                $obj->checkout_date = $arr_cart_checkout_date[$i];
                $obj->adult = $arr_cart_adult[$i];
                $obj->children = $arr_cart_children[$i];
                $obj->subtotal = number_format($sub, 2);
                $obj->save();

                while(1) {
                    if($t1>=$t2) {
                        break;
                    }
    
                    $obj = new BookedRoom();
                    $obj->booking_date = date('d/m/Y',$t1);
                    $obj->order_no = $order_no;
                    $obj->room_id = $arr_cart_room_id[$i];
                    $obj->save();
    
                    $t1 = strtotime('+1 day',$t1);
                }

            }

            $subject = 'New Order';
            $message = 'You have made an order for hotel booking. The booking information is given below: <br>';
            $message .= '<br>Order No: '.$order_no;
            $message .= '<br>Transaction Id: '.$result->id;
            $message .= '<br>Payment Method: PayPal';
            $message .= '<br>Paid Amount: '.$paid_amount;
            $message .= '<br>Booking Date: '.date('d/m/Y').'<br>';

            for($i=0;$i<count($arr_cart_room_id);$i++) {

                $r_info = Room::where('id',$arr_cart_room_id[$i])->first();

                $message .= '<br>Room Name: '.$r_info->name;
                $message .= '<br>Price Per Night: $'.$r_info->price;
                $message .= '<br>Checkin Date: '.$arr_cart_checkin_date[$i];
                $message .= '<br>Checkout Date: '.$arr_cart_checkout_date[$i];
                $message .= '<br>Adult: '.$arr_cart_adult[$i];
                $message .= '<br>Children: '.$arr_cart_children[$i].'<br>';
            }            

            $customer_email = Auth::guard('customer')->user()->email;

            \Mail::to($customer_email)->send(new Websitemail($subject,$message));

            session()->forget('cart_room_id');
            session()->forget('cart_checkin_date');
            session()->forget('cart_checkout_date');
            session()->forget('cart_adult');
            session()->forget('cart_children');
            session()->forget('billing_name');
            session()->forget('billing_email');
            session()->forget('billing_phone');
            session()->forget('billing_country');
            session()->forget('billing_address');
            session()->forget('billing_state');
            session()->forget('billing_city');
            session()->forget('billing_zip');

            return redirect()->route('home')->with('success', 'Payment is successful');
        }
        else
        {
            return redirect()->route('home')->with('error', 'Payment is failed');
        }


    }

    public function stripe(Request $request,$final_price)
    {
        $stripe_secret_key = 'sk_test_51O1o3UGCFpojC4d6ncT5FxsTcJjhanPvillpMqD2zVmdovnHt1utbuG8JdQbkyEJw2CtGYOpSdDqndeOEP2vkCSH00X36WPGsY';
        $cents = (int)($final_price*100);
        Stripe\Stripe::setApiKey($stripe_secret_key);
        $response = Stripe\Charge::create ([
            "amount" => $cents,
            "currency" => "usd",
            "source" => $request->stripeToken,
            "description" => env('APP_NAME')
        ]);

        $responseJson = $response->jsonSerialize();
        $transaction_id = $responseJson['balance_transaction'];
        $last_4 = $responseJson['payment_method_details']['card']['last4'];

        $order_no = time();

        $statement = DB::select("SHOW TABLE STATUS LIKE 'orders'");
        $ai_id = $statement[0]->Auto_increment;

        $obj = new Order();
        $obj->customer_id = Auth::guard('customer')->user()->id;
        $obj->order_no = $order_no;
        $obj->transaction_id = $transaction_id;
        $obj->payment_method = 'Stripe';
        $obj->card_last_digit = $last_4;
        $obj->paid_amount = number_format($final_price, 2);
        $obj->booking_date = date('d/m/Y');
        $obj->status = 'Completed';
        $obj->save();
        
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

        $arr_cart_price = session()->get('cart_price');

        for($i=0;$i<count($arr_cart_room_id);$i++)
        {
            $r_info = Room::where('id',$arr_cart_room_id[$i])->first();
            $d1 = explode('/',$arr_cart_checkin_date[$i]);
            $d2 = explode('/',$arr_cart_checkout_date[$i]);
            $d1_new = $d1[2].'-'.$d1[1].'-'.$d1[0];
            $d2_new = $d2[2].'-'.$d2[1].'-'.$d2[0];
            $t1 = strtotime($d1_new);
            $t2 = strtotime($d2_new);
            // $diff = ($t2-$t1)/60/60/24;
            // $sub = $arr_cart_price[$i];
            $room_price = $arr_cart_price[$i];

                     // Kiểm tra nếu giá phòng không phải là số thực
            if (!is_float($room_price)) {
                // Nếu không phải số thực, thử lấy phần tử đầu tiên trong mảng
                $room_price = is_array($room_price) ? $room_price[0] : $room_price;
            }

            // Kiểm tra lại nếu giá phòng là số thực sau khi xử lý
            if (is_float($room_price)) {
                // Tính toán tổng giá trị
                $sub = $room_price;
            }

            $obj = new OrderDetail();
            $obj->order_id = $ai_id;
            $obj->room_id = $arr_cart_room_id[$i];
            $obj->order_no = $order_no;
            $obj->checkin_date = $arr_cart_checkin_date[$i];
            $obj->checkout_date = $arr_cart_checkout_date[$i];
            $obj->adult = $arr_cart_adult[$i];
            $obj->children = $arr_cart_children[$i];
            $obj->subtotal = number_format($sub,2);
            $obj->save();

            while(1) {
                if($t1>=$t2) {
                    break;
                }

                $obj = new BookedRoom();
                $obj->booking_date = date('d/m/Y',$t1);
                $obj->order_no = $order_no;
                $obj->room_id = $arr_cart_room_id[$i];
                $obj->save();

                $t1 = strtotime('+1 day',$t1);
            }

        }

        $subject = 'New Order';
        $message = 'You have made an order for hotel booking. The booking information is given below: <br>';
        $message .= '<br>Order No: '.$order_no;
        $message .= '<br>Transaction Id: '.$transaction_id;
        $message .= '<br>Payment Method: Stripe';
        $message .= '<br>Paid Amount: '.$final_price;
        $message .= '<br>Booking Date: '.date('d/m/Y').'<br>';

        for($i=0;$i<count($arr_cart_room_id);$i++) {

            $r_info = Room::where('id',$arr_cart_room_id[$i])->first();

            $message .= '<br>Room Name: '.$r_info->name;
            $message .= '<br>Price Per Night: $'.$r_info->price;
            $message .= '<br>Checkin Date: '.$arr_cart_checkin_date[$i];
            $message .= '<br>Checkout Date: '.$arr_cart_checkout_date[$i];
            $message .= '<br>Adult: '.$arr_cart_adult[$i];
            $message .= '<br>Children: '.$arr_cart_children[$i].'<br>';
        }            

        $customer_email = Auth::guard('customer')->user()->email;

        \Mail::to($customer_email)->send(new Websitemail($subject,$message));

        session()->forget('cart_room_id');
        session()->forget('cart_checkin_date');
        session()->forget('cart_checkout_date');
        session()->forget('cart_adult');
        session()->forget('cart_children');
        session()->forget('billing_name');
        session()->forget('billing_email');
        session()->forget('billing_phone');
        session()->forget('billing_country');
        session()->forget('billing_address');
        session()->forget('billing_state');
        session()->forget('billing_city');
        session()->forget('billing_zip');

        return redirect()->route('home')->with('success', 'Payment is successful');


    }


}
