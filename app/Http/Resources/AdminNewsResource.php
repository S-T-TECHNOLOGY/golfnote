<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminNewsResource extends JsonResource
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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'content' => $this->content,
            'start_date' => $dateStart->format('Y-m-d'),
            'end_date' => $dateEnd->format('Y-m-d'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
