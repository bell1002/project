<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActiveCustomersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $customers = Customer::where('status', 'active')
        ->select('name', 'email', 'phone')
        ->get();
        $customers = $customers->map(function ($customer, $index) {
            return [
                'SL' => $index + 1,
                'Name' => $customer->name,
                'Email' => $customer->email,
                'Phone' => $customer->phone,
                // 'Total customers' => $customer->total_customers,
                // 'Total beds' => $customer->total_beds,
                // 'Total bathcustomers' => $customer->total_bathcustomers,
                // 'Total balconies' => $customer->total_balconies,
                // 'Total guests' => $customer->total_guests,

            ];
        });
    
        return $customers;
    }

    public function headings(): array
    {
        return [
            'SL',
            'Name',
            'Email',
            'Phone',
            // 'Total room',
            // 'Total bed',
            // 'Total bathroom',
            // 'Total balconies',
            // 'Total guest',

        ];
    }
}
