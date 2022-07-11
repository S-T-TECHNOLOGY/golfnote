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
            'avg_score' => !$this->userSummary ? 0 : (int)$this->userSummary->avg_score,
            'total_partner' => !$this->userSummary ? 0 : $this->userSummary->total_partner,
            'high_score' => !$this->userSummary ? 0 : (int)$this->userSummary->high_score,
            'last_score' => !$this->userSummary ? 0 : (int)$this->userSummary->last_score,
            'total_hio' => !$this->userSummary ? 0 : $this->userSummary->total_hio,
            'set_error' => !$this->userSummary ? 0 : $this->userSummary->set_error,
            'punish' => !$this->userSummary ? 0 : $this->userSummary->punish,
            'visited_score' => !$this->userSummary ? 0 : $this->userSummary->visited_score,
            'handicap_score' => !$this->userSummary ? 0 : (int)$this->userSummary->handicap_score,
            'rank_no' => $this->rank_no,
            'total_user' => $this->total_user,
            'notification_unread' => $this->notification_unread
        ];
    }
}
