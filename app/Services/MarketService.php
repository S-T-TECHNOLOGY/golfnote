<?php


namespace App\Services;


use App\Constants\Consts;
use App\Errors\OldThingErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\MarketCollection;
use App\Http\Resources\MarketResource;
use App\Http\Resources\OldThingCollection;
use App\Http\Resources\OldThingResource;
use App\Models\Market;
use App\Models\OldThing;

class MarketService
{
    public function getAll($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $markets = Market::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key . '%');
        })->orderBy('id', 'desc')->paginate($limit);

        return new MarketCollection($markets);
    }

    public function getDetail($id)
    {
        $market = Market::find($id);
        if (!$market) {
            throw new BusinessException('Không tìm thấy đồ cũ', OldThingErrorCode::OLD_THING_NOT_FOUND);
        }

        return new MarketResource($market);
    }

}