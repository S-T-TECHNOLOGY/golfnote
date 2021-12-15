<?php


namespace App\Services;


use App\Constants\RoomStatus;
use App\Errors\RoomErrorCode;
use App\Exceptions\BusinessException;
use App\Jobs\CalculateUserScoreSummary;
use App\Models\Golf;
use App\Models\Room;
use App\Models\RoomScore;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScoreService
{
    public function calculateScore($params)
    {
        $room = Room::where('id', $params['id'])->where('status', RoomStatus::GOING_ON_STATUS)->first();
        if (!$room) {
            throw new BusinessException('Không tìm thấy phòng chơi',RoomErrorCode::ROOM_NOT_FOUND);
        }

        $scores = $params['scores'];
        $userIds = collect($scores)->filter(function ($item) {
            return $item['user_id'] > 0;
        })->map(function ($item) {
            return $item['user_id'];
        })->values();
        $guestNumber = 0;
        $users = User::whereIn('id', $userIds)->get();
        $records = [];
        foreach ($scores as $item) {
            if ($item['user_id']) {
                $user = collect($users)->first(function ($user) use ($item) {
                    return $user->id === $item['user_id'];
                });
            }
            $score = collect($item['holes'])->sum('total');
            if (!$item['user_id']) {
                ++$guestNumber;
            }

            $record = [
                'room_id' => $params['id'],
                'user_id' => $item['user_id'],
                'name' => $item['user_id'] ? $user->name : 'Guest ' . $guestNumber,
                'phone' => $item['user_id'] ? $user->phone : '',
                'avatar' => $item['user_id'] ? $user->avatar : '',
                'infor' => json_encode($item['holes']),
                'score' => $score
            ];

            array_push($records, $record);
        };

        $datas = collect($records)->map(function ($item)  {
            $record = [
                'room_id' => $item['room_id'],
                'user_id' => $item['user_id'],
                'name' => $item['name'] ,
                'phone' => $item['phone'] ,
                'infor' => $item['infor'],
                'score' => $item['score']
            ];
            return $record;
        })->all();

        RoomScore::insert($datas);
        $room->status = RoomStatus::FINISHED_STATUS;
        $room->save();

        $results = collect($records)->map(function ($item) use ($room) {
            if ($item['user_id']) {
                $userScores = RoomScore::where('room_id', '!=', $room->id)
                    ->where('user_id', $item['user_id'])->orderBy('id', 'desc')->limit(5)->get();
                $avgScore = sizeof($userScores) ? ceil(collect($userScores)->avg('score')) : 0;
            }
            $result = new \stdClass();
            $result->user_id = $item['user_id'];
            $result->name = $item['name'];
            $result->phone = $item['phone'];
            $result->avatar = $item['avatar'];
            $result->score = $item['score'];
            $result->avg_score = $item['user_id'] ? $avgScore : 0;
            $result->gap_score = $item['user_id'] ? ($item['score'] - $avgScore) : 0;
            return $result;
         })->sortBy([
             ['score', 'asc']
        ])->values();

        CalculateUserScoreSummary::dispatch($scores);
        return $results;
    }

    public function history($user)
    {
        $scoreHistories = DB::table('room_scores')->join('rooms', 'room_scores.room_id', 'rooms.id')
            ->where('room_scores.user_id', $user->id)
            ->orderBy('rooms.created_at', 'desc')
            ->select('rooms.created_at', 'room_scores.score', 'rooms.golf_id')
            ->get();
        $golfIds = collect($scoreHistories)->pluck('golf_id')->values();
        $golfCourses = Golf::whereIn('id', $golfIds)->get();
        $data = collect($scoreHistories)->map(function ($item) use ($golfCourses) {
           $history = new \stdClass();
           $history->score = $item->score;
           $golfCourse = collect($golfCourses)->first(function ($golf) use ($item) {
               return $golf->id === $item->golf_id;
           });
           $history->time = Carbon::parse($item->created_at)->format('Y/m/d');
           $history->golf_name = $golfCourse->name;
           $history->golf_image = $golfCourse->image;
           return $history;
        });

        return $data;
    }
}