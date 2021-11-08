<?php


namespace App\Services;


use App\Constants\Consts;
use App\Http\Resources\UserCollection;
use App\Models\User;

class UserService
{
    public function find($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $users = User::when(!empty($key), function ($query) use ($key) {
            return $query->where(function ($query) use ($key) {
                return $query->where('account_name', 'like', '%' . $key . '%')
                            ->orWhere('phone', 'like', '%' . $key . '%');
            });
        })->where('active', 1)->where('id', '!=', $params['user_id'])->paginate($limit);

        return new UserCollection($users);
    }
}