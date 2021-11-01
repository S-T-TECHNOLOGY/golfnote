<?php


namespace App\Services;


use App\Constants\Consts;
use App\Errors\EventErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Models\Event;

class EventService
{
    public function getAll($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $now = date('Y-m-d H:i:s');
        $events = Event::where('end_date', '>=', $now)->paginate($limit);

        return new EventCollection($events);
    }

    public function getEventDetail($id)
    {
        $event = Event::find($id);
        if (!$event) {
            throw new BusinessException('Không tìm thấy sự kiện', EventErrorCode::EVENT_NOT_FOUND);
        }

        return new EventResource($event);
    }
}