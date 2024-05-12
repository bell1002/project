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
    
    public function predict_price($data_json)
{
    // Set up cURL to make the POST request
    $url = 'http://127.0.0.1:5000/predict';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_json)
    ));

    // Execute the POST request and get the response
    $result = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    // Return the result
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
    dd('Predicted room price: ' . $price);

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
        return view('Hotel.cart');
    }
  


}
