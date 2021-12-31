<?php

namespace App\Http\Resources;

use App\Constants\EventStatus;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $dateStart = Carbon::parse($this->start_date);
        $dateEnd = Carbon::parse($this->end_date);
        $status = EventStatus::IN_COMING;

        if ($dateStart->isPast()) {
            $status = EventStatus::GOING_ON;
        }

        if ($dateEnd->isPast()) {
            $status = EventStatus::THE_END;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image,
            'address' => $this->address,
            'status' => $status,
            'start_date' => $dateStart->format('Y-m-d H:i'),
            'join_fee' => $this->join_fee,
            'quantity' => $this->quantity,
            'quantity_remain' => $this->quantity_remain,
            'host' => $this->host,
            'organizational_unit' => $this->organizational_unit,
            'caddie_fee' => $this->caddie_fee,
            'green_fee' => $this->green_fee
        ];
    }
}
