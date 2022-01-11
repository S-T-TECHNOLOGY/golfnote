<?php

namespace App\Http\Resources;

use App\Constants\NotificationType;
use App\Models\User;
use App\Models\UserRequestFriend;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->type === NotificationType::RECEIVED_REQUEST_FRIEND) {
            $requestFriend = UserRequestFriend::where('id', $this->request_friend_id)->first();
            $user = User::select('id', 'name', 'avatar')->where('id', $requestFriend->sender_id)->first();
        }
        if ($this->type === NotificationType::OTHER) {
            $data = json_decode($this->info);
        }

        return [
            'id' => $this->id,
            'type' => $this->type,
            'time_created' => $this->created_at->timestamp,
            'is_read' => $this->is_read,
            'user' => $this->type === NotificationType::RECEIVED_REQUEST_FRIEND ? $user : new \stdClass(),
            'golf' => $this->type === NotificationType::REGISTER_GOLF_SUCCESS ? new GolfResource($this->golf) : new \stdClass(),
            'event' => $this->type === NotificationType::REGISTER_EVENT_SUCCESS ? new EventResource($this->event) : new \stdClass(),
            'title' => $this->type === NotificationType::OTHER ? $data['title'] : '',
            'content' => $this->type === NotificationType::OTHER ? $data['content'] : '',
            'image' => $this->type === NotificationType::OTHER ? $data['image'] : '',
        ];
    }
}
