<?php


namespace App\Services;

use App\Constants\Consts;
use App\Http\Resources\ResortCollection;
use App\Models\Resort;

class ResortService
{
    public function getAll($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $resorts = Resort::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key . '%');
        })->orderBy('id', 'desc')->paginate($limit);

        return new ResortCollection($resorts);
    }
}