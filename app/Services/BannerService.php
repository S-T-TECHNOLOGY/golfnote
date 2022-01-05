<?php


namespace App\Services;


use App\Models\Banner;
use App\Utils\UploadUtil;
use Carbon\Carbon;

class BannerService
{
    public function create($params)
    {
        $params['image'] = UploadUtil::saveBase64ImageToStorage($params['image'], 'banner');
        $params['title'] = 'Banner';
        $params['content'] = 'Banner';
        $params['expired_date'] = date('Y-m-d');;
        $banner = Banner::create($params);

        return $banner;
    }
}