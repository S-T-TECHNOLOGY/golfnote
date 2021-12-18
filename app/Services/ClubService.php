<?php


namespace App\Services;


use App\Constants\Consts;
use App\Http\Resources\UserClubCollection;
use App\Models\UserClub;

class ClubService
{
    public function getAll($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $clubs = UserClub::paginate($limit);
        return new UserClubCollection($clubs);
    }
}