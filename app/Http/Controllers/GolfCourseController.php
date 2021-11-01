<?php


namespace App\Http\Controllers;


use App\Services\GolfCourService;
use Illuminate\Http\Request;


class GolfCourseController extends AppBaseController
{
    protected $golfCourseService;

    public function __construct(GolfCourService $golfCourService)
    {
        $this->golfCourseService = $golfCourService;
    }

    public function getGolfCourses(Request $request)
    {
        $data = $this->golfCourseService->getGolfCourse($request->all());
        return $this->sendResponse($data);
    }

    public function getGolfCourseDetail($id)
    {
        $golfCourse = $this->golfCourseService->getGolfCourseDetail($id);
        return $this->sendResponse($golfCourse);
    }

}