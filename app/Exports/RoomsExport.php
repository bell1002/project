<?php

namespace App\Exports;

use App\Models\Room;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RoomsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $rooms = Room::select('id', 'name', 'description','price','total_rooms','total_beds','total_bathrooms','total_balconies','total_guests')->get();
        $rooms = $rooms->map(function ($room, $index) {
            return [
                'SL' => $index + 1,
                'ID' => $room->id,
                'Name' => $room->name,
                'Description' => $room->description,
                'Price' => $room->price,
                'Total rooms' => $room->total_rooms,
                'Total beds' => $room->total_beds,
                'Total bathrooms' => $room->total_bathrooms,
                'Total balconies' => $room->total_balconies,
                'Total guests' => $room->total_guests,

            ];
        });
    
        return $rooms;
    }

    public function headings(): array
    {
        return [
            'SL',
            'ID',
            'Name',
            'Description',
            'Price',
            'Total room',
            'Total bed',
            'Total bathroom',
            'Total balconies',
            'Total guest',

        ];
    }
}
