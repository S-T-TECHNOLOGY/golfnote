<?php


namespace App\Services;


use App\Constants\RoomStatus;
use App\Errors\RoomErrorCode;
use App\Events\RoomDraftScoreEvent;
use App\Exceptions\BusinessException;
use App\Jobs\CalculateUserScoreSummary;
use App\Models\Golf;
use App\Models\Room;
use App\Models\RoomDraftScore;
use App\Models\RoomScore;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScoreService
{
    public function calculateScore($params, $isAdmin = false)
    {
        if ($isAdmin) {
            $room = Room::where('id', $params['id'])->where('status', RoomStatus::HANDLE_SCORE)->first();
        } else {
            $room = Room::where('id', $params['id'])->where('owner_id', $params['user_id'])->where('status', RoomStatus::GOING_ON_STATUS)->first();
        }

        if (!$room) {
            throw new BusinessException('Không tìm thấy phòng chơi',RoomErrorCode::ROOM_NOT_FOUND);
        }

        $scores = $params['scores'];
        $userIds = collect($scores)->filter(function ($item) {
            return $item['user_id'] > 0;
        })->map(function ($item) {
            return $item['user_id'];
        })->values();
        $users = User::whereIn('id', $userIds)->get();
        $records = [];
        $isCompleted = false;
        foreach ($scores as $item) {
            if ($item['user_id']) {
                $user = collect($users)->first(function ($user) use ($item) {
                    return $user->id === $item['user_id'];
                });
            }
            $completeHoles = collect($item['holes'])->filter(function ($hole) {
                return $hole['total'] > 0;
            })->toArray();
            $isCompleted = sizeof($completeHoles) === 18 ? true : false;
            $score = collect($item['holes'])->sum('total');

            $record = [
                'room_id' => $params['id'],
                'user_id' => $item['user_id'],
                'name' => $item['user_id'] ? $user->name : $item['name'],
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
                'score' => $item['score'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            return $record;
        })->all();

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

        if ($isCompleted) {
            $draftScoreParams = [
                'infor' => json_encode($params['scores']),
                'room_id' => $params['id'],
                'hole_current' => 18
            ];
            RoomDraftScore::updateOrCreate(
                ['room_id' => $params['id']],
                $draftScoreParams);
            RoomScore::insert($datas);
            CalculateUserScoreSummary::dispatch($scores);
        }

        return $results;
    }

    public function history($user)
    {
        $scoreHistories = DB::table('room_scores')->join('rooms', 'room_scores.room_id', 'rooms.id')
            ->where('room_scores.user_id', $user->id)
            ->orderBy('room_scores.created_at', 'desc')
            ->select('rooms.created_at', 'room_scores.score', 'rooms.golf_id', 'room_scores.room_id')
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
           $history->room_id = $item->room_id;
           return $history;
        });

        return $data;
    }

    public function logDraftScore($params)
    {
        $room = Room::where('id', $params['room_id'])->where('owner_id', $params['user_id'])->where('status', RoomStatus::GOING_ON_STATUS)->first();
        if (!$room) {
            throw new BusinessException('Không tìm thấy phòng chơi',RoomErrorCode::ROOM_NOT_FOUND);
        }

        $params['infor'] = json_encode($params['scores']);
        $params['updated_at'] =  Carbon::now()->format('d/m/Y h:m:s');
        RoomDraftScore::updateOrCreate(
            ['room_id' => $params['room_id']],
            $params);
        event(new RoomDraftScoreEvent($params['scores'], $params['room_id'], $params['hole_current']));
        return new \stdClass();
    }
}