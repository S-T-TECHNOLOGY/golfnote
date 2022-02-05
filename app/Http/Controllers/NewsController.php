<?php

namespace App\Http\Controllers;

use App\Constants\Consts;
use App\Http\Resources\NewsCollection;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends AppBaseController
{
    public function getAll(Request $request)
    {
        $params = $request->all();
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $news = News::when(!empty($key), function ($query) use ($key){
            return $query->where(function ($query) use ($key) {
                return $query->where('title', 'like', '%'.$key.'%')
                    ->orWhere('description', 'like', '%'.$key.'%');
            });
        })->paginate($limit);
        return $this->sendResponse(new NewsCollection($news));
    }
}
