<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $orders = Order::select('order_no', 'payment_method', 'booking_date','paid_amount','status')->get();
        $orders = $orders->map(function ($order, $index) {
            return [
                'SL' => $index + 1,
                'Order No' => $order->order_no,
                'Payment Method' => $order->payment_method,
                'Booking Date' => $order->booking_date,
                'Paid Amount' => $order->paid_amount,
                'Status' => $order->status,
            ];
        });
    
        return $orders;
    }

    public function headings(): array
    {
        return [
            'SL',
            'Order No',
            'Payment Method',
            'Booking Date',
            'Paid Amount',
            'Status',
        ];
    }
}
