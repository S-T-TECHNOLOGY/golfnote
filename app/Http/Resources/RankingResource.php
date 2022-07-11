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
            'active' => $this->user->active,
            'total_round' => $this->total_round,
            'avg_score' => (int)$this->avg_score,
            'total_partner' => (int)$this->total_partner,
            'high_score' => (int)$this->high_score,
            'last_score' => (int)$this->last_score,
            'total_hio' => $this->total_hio,
            'set_error' => $this->set_error,
            'punish' => $this->punish,
            'visited_score' => $this->visited_score,
            'handicap_score' => (int)$this->handicap_score,
            'rank_no' => $this->rank_no
        ];
    }
}
