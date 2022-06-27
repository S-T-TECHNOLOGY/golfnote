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
        $scoreFactor = 0.96;
        $userScores = collect($this->scores)->filter(function ($score) {
            return $score['user_id'] > 0;
        })->values();
        foreach ($userScores as $score) {
            $userSummary = UserSummary::where('user_id', $score['user_id'])->first();
            $userScore = collect($score['holes'])->sum('total');
            $highScore = $userScore;
            $totalFail = collect($score['holes'])->sum('penalty');
            $totalPut = collect($score['holes'])->sum('put');
            $scores = RoomScore::where('user_id', $score['user_id'])->orderBy('id', 'desc')->get();
            $avgScore = collect($scores)->skip(0)->take(10)->avg('score');
            $totalMatch = sizeof($scores);
            $handicapScore = 0;
            $scores = collect($scores)->sortBy('score');
            if ($totalMatch >= 3 && $totalMatch <= 6) {
                $handicapScore = $scores->skip(0)->take(1)->avg('score');
            }

            if ($totalMatch >= 7 && $totalMatch <= 8) {
                $handicapScore = $scores->skip(0)->take(2)->avg('score');
            }

            if ($totalMatch >= 9 && $totalMatch <= 10) {
                $handicapScore = $scores->skip(0)->take(3)->avg('score');
            }

            if ($totalMatch >= 11 && $totalMatch <= 12) {
                $handicapScore = $scores->skip(0)->take(4)->avg('score');
            }

            if ($totalMatch >= 13 && $totalMatch <= 14) {
                $handicapScore = $scores->skip(0)->take(5)->avg('score');
            }

            if ($totalMatch >= 15 && $totalMatch <= 16) {
                $handicapScore = $scores->skip(0)->take(6)->avg('score');
            }

            if ($totalMatch === 17) {
                $handicapScore = $scores->skip(0)->take(7)->avg('score');
            }

            if ($totalMatch === 18) {
                $handicapScore = $scores->skip(0)->take(8)->avg('score');
            }

            if ($totalMatch === 19) {
                $handicapScore = $scores->skip(0)->take(9)->avg('score');
            }

            if ($totalMatch >= 20) {
                $handicapScore = $scores->skip(0)->take(10)->avg('score');
            }

            $handicapScore = $handicapScore * $scoreFactor;

            $hioTotal = collect($score['holes'])->filter(function ($item) {
                return ($item['standard'] - $item['total']) >= 2;
            })->values()->count();
            $roundTotal = 1;
            $visitedScore = 1;
            $totalPartner = sizeof($userScores) - 1;

            if ($userSummary) {
                $highScore = $highScore < $userSummary->high_score ? $highScore : $userSummary->high_score;
                $hioTotal += $userSummary->total_hio;
                $roundTotal += $userSummary->total_round;
                $totalPartner += $userSummary->total_partner;
                $visitedScore += $userSummary->visited_score;
            }

            $data = [
                'user_id' => $score['user_id'],
                'total_round' => $roundTotal,
                'total_partner' => $totalPartner,
                'high_score' => $highScore,
                'last_score' => $userScore,
                'total_hio' => $hioTotal,
                'set_error' => $totalFail / 18,
                'punish' => $totalPut / 18,
                'visited_score' => $visitedScore,
                'avg_score' => floatval(number_format($avgScore, 2, '.', '')),
                'handicap_score' => floatval(number_format($handicapScore, 2, '.', ''))
            ];

            UserSummary::updateOrCreate(
                ['user_id' => $score['user_id']],
                $data
            );
        }
    }
}
