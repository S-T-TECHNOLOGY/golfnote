<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
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
            'gender' => $this->gender,
            'avatar' => $this->avatar,
            'phone' => $this->phone,
            'address' => $this->address,
            'total_round' => !$this->userSummary ? 0 : $this->userSummary->total_round,
            'total_course' => !$this->userSummary ? 0 : $this->userSummary->total_course,
            'total_partner' => !$this->userSummary ? 0 : $this->userSummary->total_partner,
            'high_score' => !$this->userSummary ? 0 : $this->userSummary->high_score,
            'total_score' => !$this->userSummary ? 0 : $this->userSummary->total_score,
            'total_hio' => !$this->userSummary ? 0 : $this->userSummary->total_hio,
            'total_fail' => !$this->userSummary ? 0 : $this->userSummary->total_fail,
            'total_punish' => !$this->userSummary ? 0 : $this->userSummary->total_punish,
            'visited_score' => !$this->userSummary ? 0 : $this->userSummary->visited_score,
            'handicap_score' => !$this->userSummary ? 0 : $this->userSummary->handicap_score,
            'rank_no' => $this->rank_no
        ];
    }
}
