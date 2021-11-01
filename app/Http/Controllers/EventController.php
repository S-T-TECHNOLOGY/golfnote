<?php


namespace App\Http\Controllers;


use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends AppBaseController
{
    protected $eventService;
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function getAll(Request $request)
    {
        $events = $this->eventService->getAll($request->all());
        return $this->sendResponse($events);
    }

    public function getEventDetail($id)
    {
        $event = $this->eventService->getEventDetail($id);
        return $this->sendResponse($event);
    }
}