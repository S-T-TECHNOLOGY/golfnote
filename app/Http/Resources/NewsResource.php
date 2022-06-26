<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'content' => $this->content,
            'image' => $this->image,
            'description' => $this->description,
            'start_date' => $dateStart->format('Y-m-d'),
            'end_date' => $dateEnd->format('Y-m-d'),
            'created_at' => $this->created_at->timestamp
        ];
    }
}
