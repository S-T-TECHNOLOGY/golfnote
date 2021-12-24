<?php


namespace App\Services;


use App\Models\Banner;
use App\Utils\UploadUtil;

class BannerService
{
    public function create($params)
    {
        $params['image'] = UploadUtil::saveBase64ImageToStorage($params['image'], 'banner');
        $banner = Banner::create($params);

        return $banner;
    }
}