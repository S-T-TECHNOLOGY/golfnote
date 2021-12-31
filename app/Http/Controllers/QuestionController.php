<?php

namespace App\Http\Controllers;

use App\Services\QuestionService;
use Illuminate\Http\Request;

class QuestionController extends AppBaseController
{

    protected $questionService;
    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    public function getAll(Request $request)
    {
        $data = $this->questionService->getAll($request->all());
        return $this->sendResponse($data);
    }
}
