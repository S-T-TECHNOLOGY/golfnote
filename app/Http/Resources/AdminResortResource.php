<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResortResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => json_decode($this->image),
            'price' => intval($this->price),
            'phone' => $this->phone,
            'sale_off' => $this->sale_off,
            'quantity' => $this->quantity,
            'description' => $this->description
        ];
    }
}
