<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class ActiveCustomersExport implements FromCollection, WithHeadings
{
    protected $customers;

    public function __construct(Collection $customers)
    {
        $this->customers = $customers;
    }

    public function collection()
    {
        return $this->customers->map(function ($customer, $index) {
            return [
                'SL' => $index + 1,
                'Name' => $customer->name,
                'Email' => $customer->email,
                'Phone' => $customer->phone,
                'Country' => $customer->country,
                'Address' => $customer->address,
                'City' => $customer->city
            ];
        });
    }

    public function headings(): array
    {
        return [
            'SL',
            'Name',
            'Email',
            'Phone',
            'Country',
            'Address',
            'City'
        ];
    }
}
