<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\OldThing;
use App\Models\UserCheckIn;
use App\Models\UserClub;
use App\Models\UserEventReservation;
use App\Models\UserRequestFriend;
use App\Models\UserReservation;
use App\Models\UserSummary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveUserInformation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $userId;
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userId = $this->userId;
        OldThing::where('user_id', $this->userId)->delete();
        Notification::where('user_id', $this->userId)->delete();
        UserClub::where('user_id', $this->userId)->delete();
        UserReservation::where('user_id', $this->userId)->delete();
        UserEventReservation::where('user_id', $this->userId)->delete();
        UserCheckIn::where('user_id', $this->userId)->delete();
        UserSummary::where('user_id', $this->userId)->delete();
        UserRequestFriend::where(function ($query) use ($userId){
            return $query->where('sender_id', $userId)->orWhere('received_id', $userId);
        })->delete();
    }
}
