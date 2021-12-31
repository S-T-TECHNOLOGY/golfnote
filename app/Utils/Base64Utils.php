<?php


namespace App\Utils;


class Base64Utils
{
    public static function checkIsBase64($text)
    {
        return str_contains($text, 'data:image/');
    }
}