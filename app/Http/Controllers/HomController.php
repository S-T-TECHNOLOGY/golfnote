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
        $link = 'http://server1.hanbisoft.com/storage/golfnote/security.docx';
        return view('preview', ['link' => $link]);
    }
}
