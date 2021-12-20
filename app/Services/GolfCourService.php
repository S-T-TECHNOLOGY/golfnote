<?php


namespace App\Services;


use App\Constants\Consts;
use App\Errors\GolfCourseErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\GolfCollection;
use App\Http\Resources\GolfResource;
use App\Models\Golf;
use App\Models\GolfHole;
use App\Models\HoleImage;
use App\Utils\UploadUtil;

class GolfCourService
{
    public function getGolfs($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $golfCourses = Golf::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%'.$key.'%' );
        })->orderBy('id', 'desc')->paginate($limit);

        return new GolfCollection($golfCourses);
    }

    public function getGolfCourseDetail($id)
    {
        $golfCourse = Golf::where('id', $id)->first();
        if (!$golfCourse) {
            throw new BusinessException('Không tìm thấy sân golf', GolfCourseErrorCode::GOLF_COURSE_NOT_FOUND);
        }

        return new GolfResource($golfCourse);
    }

    public function getGolfCourses($params)
    {
        $golfCourses = [];
        foreach ($params['courses'] as $course) {
            $courses = HoleImage::select('image', 'course', 'number_hole')->where('golf_id', $params['id'])->where('course', $course)->get();
            $golfCourses = array_merge($golfCourses, $courses->toArray());
        }

        return $golfCourses;
    }

    public function createGolf($params)
    {
        $params['image'] = UploadUtil::saveBase64ImageToStorage($params['image'], 'golf');
        $courseA = isset($params['course_a']) ? $params['course_a'] :[];
        $courseB = isset($params['course_b']) ? ($params['course_b']) :[];
        $courseC = isset($params['course_c']) ? ($params['course_c']) :[];
        $courseD = isset($params['course_d']) ? ($params['course_d']) :[];
        $params['is_open'] = 1;
        $params['number_hole'] = sizeof($courseA) + sizeof($courseB) + sizeof($courseC) + sizeof($courseD);
        $courses = [];
        if (sizeof($courseA)){
            array_push($courses, 'A');
        }
        if (sizeof($courseB)){
            array_push($courses, 'B');
        }
        if (sizeof($courseC)){
            array_push($courses, 'C');
        }
        if (sizeof($courseD)){
            array_push($courses, 'D');
        }
        $params['courses'] = json_encode($courses);
        $golf = Golf::create($params);
        $holeImagesCourseA = $this->uploadHoleImagesByCourse($courseA, 'A', $golf->id);
        $holeImagesCourseB = $this->uploadHoleImagesByCourse($courseB, 'B', $golf->id);
        $holeImagesCourseC = $this->uploadHoleImagesByCourse($courseC, 'C', $golf->id);
        $holeImagesCourseD = $this->uploadHoleImagesByCourse($courseD, 'D', $golf->id);
        $holeImages = array_merge($holeImagesCourseA, $holeImagesCourseB, $holeImagesCourseC, $holeImagesCourseD);
        HoleImage::insert($holeImages);
        return $golf;
    }

    private function uploadHoleImagesByCourse($images, $course, $golfId)
    {
        $holeImages = [];
        foreach ($images as $index => $image) {

            $holeImage['golf_id'] = $golfId;
            $holeImage['course'] = $course;
            $holeImage['image'] = UploadUtil::saveBase64ImageToStorage($image, 'golf');
            $holeImage['number_hole'] = $index + 1;
            array_push($holeImages, $holeImage);
        }

        return $holeImages;
    }
}