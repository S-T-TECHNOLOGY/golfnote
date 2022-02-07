<?php

namespace App\Exports;

use App\Models\UserCheckIn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CheckInStoreExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $storeId;
    protected $name;
    public function __construct($storeId, $name)
    {
        $this->storeId = $storeId;
        $this->name = $name;
    }

    public function collection()
    {
        return UserCheckIn::where('store_id', $this->storeId)->with('user')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Set header columns
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'name',
            'phone',
            'time'
        ];
    }

    /**
     * Mapping data
     *
     * @return array
     */
    public function map($checkin): array
    {
        return [
            $checkin->user->name,
            $checkin->user->phone,
            $checkin->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
