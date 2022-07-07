<?php

namespace App\Exports;

use App\Models\UserReservation;
use App\Utils\FormatUserId;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GolfReservation implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $status;
    protected $golfId;
    public function __construct($status, $golfId)
    {
        $this->status  = $status;
        $this->golfId = $golfId;
    }


    public function collection()
    {
        $status = $this->status;
        $golfId = $this->golfId;
        $reservations = UserReservation::when(strlen($status), function ($query) use ($status) {
            return $query->where('status', $status);
        })->when($golfId, function ($query) use ($golfId) {
            return $query->where('golf_id', $golfId);
        })->with('golf')->orderBy('created_at', 'desc')->get();

        return $reservations;
    }

    public function headings(): array
    {
        return ['User code', 'User name', 'Email', 'Phone', 'Total Player', 'Date', 'Golf Name', 'Golf Address', 'Golf Phone', 'Comment', 'Status', 'Created at'];
    }

    public function map($reservation): array
    {
        return [
            FormatUserId::formatUserId($reservation->user_id),
            $reservation->user_name,
            $reservation->email,
            $reservation->phone,
            $reservation->total_player,
            $reservation->date,
            $reservation->golf->name,
            $reservation->golf->address,
            $reservation->golf->phone,
            $reservation->note,
            $this->convertStatus($reservation->status),
            $reservation->created_at->format('Y-m-d H:i:s')
        ];
    }

    private function convertStatus($status)
    {
        if ($status === 0) {
            return 'Pending';
        }

        if ($status === 1) {
            return 'Done';
        }

        if ($status === -1) {
            return 'Canceled';
        }

        return '';
    }
}
