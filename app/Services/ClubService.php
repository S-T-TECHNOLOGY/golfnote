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
        $key = isset($params['key']) ? $params['key'] : Consts::LIMIT_DEFAULT;
        $clubs = UserClub::when(!empty($key), function ($query) use ($key){
            return $query->where(function ($query) use ($key) {
                return $query->where('name', 'like', '%'.$key.'%')
                    ->orWhere('introduction', 'like', '%'.$key.'%');
            });
        })->paginate($limit);
        return new UserClubCollection($clubs);
    }
}