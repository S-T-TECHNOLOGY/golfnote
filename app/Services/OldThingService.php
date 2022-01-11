<?php


namespace App\Services;


use App\Constants\Consts;
use App\Errors\OldThingErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\OldThingCollection;
use App\Http\Resources\OldThingResource;
use App\Models\OldThing;

class OldThingService
{

    public function getAll($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $oldThings = OldThing::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key . '%');
        })->orderBy('id', 'desc')->paginate($limit);

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