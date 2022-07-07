<?php

namespace App\Exports;

use App\Models\UserEventReservation;
use App\Utils\FormatUserId;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventReservation implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $status;
    public function __construct($status)
    {
        $this->status = $status;
    }

    public function collection()
    {
        $status = $this->status;
        $reservations = UserEventReservation::when(strlen($status), function ($query) use ($status) {
            return $query->where('status', $status);
        })->with('event')->orderBy('created_at', 'desc')->get();

        return $reservations;
    }

    public function headings(): array
    {
        return ['User code', 'User name', 'Email', 'Phone', 'Event Name', 'Event Address', 'Comment', 'Status', 'Created at'];
    }

    public function map($reservation): array
    {
        return [
            FormatUserId::formatUserId($reservation->user_id),
            $reservation->user_name,
            $reservation->email,
            $reservation->phone,
            $reservation->event->name,
            $reservation->event->address,
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
