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
            'total_course' => $this->total_course,
            'total_partner' => $this->total_partner,
            'high_score' => $this->high_score,
            'total_score' => $this->total_score,
            'total_hio' => $this->total_hio,
            'total_fail' => $this->total_fail,
            'total_punish' => $this->total_punish,
            'visited_score' => $this->visited_score,
            'handicap_score' => $this->handicap_score,
            'rank_no' => $this->rank_no
        ];
    }
}
