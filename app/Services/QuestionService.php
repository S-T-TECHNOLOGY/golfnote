<?php


namespace App\Services;


use App\Constants\Consts;
use App\Http\Resources\QuestionCollection;
use App\Models\Question;

class QuestionService
{
    public function getAll($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $questions = Question::paginate($limit);
        return new QuestionCollection($questions);
    }
}