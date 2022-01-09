<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetHoleStandardRequest;
use App\Models\HoleImage;

class GolfHoleController extends AppBaseController
{
    public function getHoleByType(GetHoleStandardRequest $request)
    {
        $golfCourses = $request->golf_courses;
        $golfId = $request->golf_id;
        $holeCourseA = HoleImage::select('number_hole', 'standard')->where('golf_id', $golfId)->where('course', $golfCourses[0])->get();
        $holeCourseB = HoleImage::select('number_hole', 'standard')->where('golf_id', $golfId)->where('course', $golfCourses[1])->get();
        $holeCourseB = $holeCourseB->map(function ($hole) {
            $hole['number_hole'] = $hole['number_hole'] + 9;
            return $hole;
        })->toArray();
        $golfHoles = array_merge($holeCourseA->toArray(), $holeCourseB);
        return $this->sendResponse($golfHoles);
    }
}
