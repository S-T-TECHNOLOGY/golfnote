<?php

namespace App\Http\Controllers;

class HomController extends Controller
{
    public function about()
    {
        return view('about');
    }

    public function previewFile($file)
    {
        $link = env('APP_URL') .'/storage/golfnote/'.$file;
        return view('preview', ['link' => $link]);
    }
}
