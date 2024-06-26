@extends('hotel.layout.app')

@section('main_content')
<div class="page-top">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $global_page_data->cart_heading }}</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <div class="row cart">
            <div class="col-md-12">
                

                @if(session()->has('cart_room_id'))

                <div class="table-responsive">
                    <table class="table table-bordered table-cart">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Serial</th>
                                <th>Photo</th>
                                <th>Room Info</th>
                                <th>Price/Night</th>
                                <th>Checkin</th>
                                <th>Checkout</th>
                                <th>Guests</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
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

                            $total_price = 0;
                            for($i=0;$i<count($arr_cart_room_id);$i++)
                            {
                                $room_data = DB::table('rooms')->where('id',$arr_cart_room_id[$i])->first();
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('cart_delete',$arr_cart_room_id[$i]) }}" class="cart-delete-link" onclick="return confirm('Are you sure?');"><i class="fa fa-times"></i></a>
                                    </td>
                                    <td>{{ $i+1 }}</td>
                                    <td><img src="{{ asset('uploads/'.$room_data->featured_photo) }}"></td>
                                    <td>
                                        <a href="{{ route('room_detail',$room_data->id) }}" class="room-name">{{ $room_data->name }}</a>
                                    </td>
                                    <td>${{ $room_data->price }}</td>
                                    <td>{{ $arr_cart_checkin_date[$i] }}</td>
                                    <td>{{ $arr_cart_checkout_date[$i] }}</td>
                                    <td>
                                        Adult: {{ $arr_cart_adult[$i] }}<br>
                                        Children: {{ $arr_cart_children[$i] }}
                                    </td>
                                    <td>
                                    @php
                                        $d1 = explode('/',$arr_cart_checkin_date[$i]);
                                        $d2 = explode('/',$arr_cart_checkout_date[$i]);
                                        $d1_new = $d1[2].'-'.$d1[1].'-'.$d1[0];
                                        $d2_new = $d2[2].'-'.$d2[1].'-'.$d2[0];
                                        $t1 = strtotime($d1_new);
                                        $t2 = strtotime($d2_new);
                                        $diff = ($t2-$t1)/60/60/24;
                                        // Lấy giá phòng từ session và chuyển đổi thành một số thực
                                        $room_price = session()->get('cart_price')[$i];

                                        // Kiểm tra nếu giá phòng không phải là số thực
                                        if (!is_float($room_price)) {
                                            // Nếu không phải số thực, thử lấy phần tử đầu tiên trong mảng
                                            $room_price = is_array($room_price) ? $room_price[0] : $room_price;
                                        }

                                        // Kiểm tra lại nếu giá phòng là số thực sau khi xử lý
                                        if (is_float($room_price)) {
                                            // Tính toán tổng giá trị
                                           // $room_price ;
                                            echo '$' . number_format($room_price, 2);
                                        } else {
                                            echo 'Invalid room price';
                                        } // Format giá phòng để hiển thị đúng dạng tiền tệ
                                    @endphp
                                    </td>
                                </tr>
                                @php
                                //$total_price += $room_price;
                                $total_price += (float) $room_price;
                            }
                            @endphp                            
                            <tr>
                                <td colspan="8" class="tar">Total:</td>
                                <td>${{ number_format($total_price, 2) }}</td>
                                
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="checkout mb_20">
                    <a href="{{ route('checkout') }}" class="btn btn-primary bg-website">Checkout</a>
                </div>

                @else

                <div class="text-danger mb_30">
                    Cart is empty!
                </div>

                @endif

            </div>
        </div>
    </div>
</div>
@endsection