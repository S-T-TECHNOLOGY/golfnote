<?php


namespace App\Services;


use App\Constants\Consts;
use App\Constants\OldThingType;
use App\Errors\OldThingErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\OldThingCollection;
use App\Http\Resources\OldThingResource;
use App\Models\OldThing;

class OldThingService
{

    public function getAll($params, $user)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $type = isset($params['type']) ? $params['type'] : OldThingType::TYPE_SELLING;
        $query = OldThing::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key . '%');
        });
        switch ($type) {
            case  OldThingType::TYPE_SELLING:
                $query = $query->where('quantity_remain', '>', '0');
                break;
            case OldThingType::TYPE_MY:
                $query = $query->where('user_id', $user->id);
                break;
            case OldThingType::SOLD_OUT:
                $query = $query->where('quantity_remain', '0');
                break;
        }

        $oldThings = $query->orderBy('id', 'desc')->paginate($limit);
        return new OldThingCollection($oldThings);
    }

    public function getDetail($id)
    {
        $oldThing = OldThing::find($id);
        if (!$oldThing) {
            throw new BusinessException('Không tìm thấy đồ cũ', OldThingErrorCode::OLD_THING_NOT_FOUND);
        }

        return new OldThingResource($oldThing);
    }

    public function soldOut($id, $user)
    {
        $oldThing = OldThing::where('id', $id)->where('user_id', $user->id)->first();
        if (!$oldThing) {
            throw new BusinessException('Không tìm thấy đồ cũ', OldThingErrorCode::OLD_THING_NOT_FOUND);
        }

        $oldThing->quantity_remain = 0;
        $oldThing->save();

        return new \stdClass();
    }
}