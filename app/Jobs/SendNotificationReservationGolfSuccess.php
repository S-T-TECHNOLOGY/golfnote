<?php


namespace App\Jobs;


use App\Constants\NotificationType;
use App\Models\User;
use App\Traists\PushNotificationTraist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationReservationGolfSuccess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $userId;
    protected $data;

    use PushNotificationTraist;

    public function __construct($userId, $data)
    {
        $this->userId = $userId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::find($this->userId);
        $token = $user->fcm_token;
        $device = $user->device;
        if (!empty($token)) {
            $this->pushMessage($token, $this->data, $device);
        }

    }

}