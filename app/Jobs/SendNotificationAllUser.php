<?php

namespace App\Jobs;

use App\Constants\NotificationType;
use App\Models\Notification;
use App\Traists\PushNotificationTraist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationAllUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $users;
    protected $data;
    use PushNotificationTraist;
    public function __construct($users, $data)
    {
        $this->users = $users;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->users as $user) {
            if ($user->setting_notification) {
                $data = [
                    'type' => NotificationType::OTHER,
                    'user_id' => $user->id,
                    'info' => json_encode($this->data)
                ];
                Notification::create($data);
                $this->pushMessage($user->fcm_token, $this->data, $user->device);
            }
        }
    }
}
