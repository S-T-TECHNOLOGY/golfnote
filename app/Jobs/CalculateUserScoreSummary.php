<?php

namespace App\Jobs;

use App\Constants\RoomStatus;
use App\Models\RoomPlayer;
use App\Models\RoomScore;
use App\Models\UserSummary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateUserScoreSummary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $scores;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($scores)
    {
        $this->scores = $scores;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userScores = collect($this->scores)->filter(function ($score) {
           return $score['user_id'] > 0;
        })->values();
        foreach ($userScores as $score) {
            $userSummary = UserSummary::where('user_id', $score['user_id'])->first();
            $userScore = collect($score['holes'])->sum('total');
            $highScore = $userScore;
            $totalFail = collect($score['holes'])->sum('penalty');
            $handicapScore = ceil(RoomScore::where('user_id', $score['user_id'])->orderBy('id', 'desc')->limit(5)->avg('score'));
            $rooms = RoomPlayer::where('user_id', $score['user_id'])->pluck('room_id')->toArray();
            $totalPartner = RoomPlayer::where('user_id', '!=' , $score['user_id'])->where('user_id', '>', 0)
                ->whereIn('room_id', $rooms)->distinct()->count('user_id');
            $hioTotal = collect($score['holes'])->filter(function ($item) {
                return ($item['standard'] - $item['total']) >= 2;
            })->values()->count();
            $roundTotal = 1;
            $courseTotal = 2;
            $visitedScore = 0;

            if ($userSummary) {
                $userScore += $userSummary->total_score;
                $highScore = $highScore < $userSummary->high_score ? $highScore : $userSummary->high_score;
                $totalFail += $userSummary->total_fail;
                $hioTotal += $userSummary->total_hio;
                $roundTotal += $userSummary->total_round;
                $courseTotal += $userSummary->total_course;
            }

            $data = [
                'user_id' => $score['user_id'],
                'total_round' => $roundTotal,
                'total_course' => $courseTotal,
                'total_partner' => $totalPartner,
                'high_score' => $highScore,
                'total_score' => $userScore,
                'total_hio' => $hioTotal,
                'total_fail' => $totalFail,
                'total_punish' => $totalFail,
                'visited_score' => $visitedScore,
                'handicap_score' => $handicapScore
            ];

            UserSummary::updateOrCreate(
                ['user_id' => $score['user_id']],
                $data
            );
        }
    }
}
