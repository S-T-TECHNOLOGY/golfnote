<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RankingResource extends JsonResource
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
            'name' => $this->user->name,
            'gender' => $this->user->gender,
            'avatar' => $this->user->avatar,
            'phone' => $this->user->phone,
            'address' => $this->user->address,
            'total_round' => $this->total_round,
            'avg_score' => $this->avg_score,
            'total_partner' => $this->total_partner,
            'high_score' => $this->high_score,
            'last_score' => $this->last_score,
            'total_hio' => $this->total_hio,
            'set_error' => $this->set_error,
            'punish' => $this->punish,
            'visited_score' => $this->visited_score,
            'handicap_score' => $this->handicap_score,
            'rank_no' => $this->rank_no
        ];
    }
}
