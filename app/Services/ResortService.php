<?php


namespace App\Services;

use App\Constants\Consts;
use App\Errors\ResortErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\ResortCollection;
use App\Http\Resources\ResortResource;
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

    public function getDetail($id)
    {
        $resort = Resort::find($id);
        if (!$resort) {
            throw new BusinessException('Resort not found', ResortErrorCode::RESORT_NOT_FOUND);
        }

        return new ResortResource($resort);
    }
}