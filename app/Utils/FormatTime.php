<?php


namespace App\Utils;


class FormatTime
{
    public static function convertTime($time) {
        $hour = (int) ($time / 60);
        $min = !($time % 60) ? '00' : $time % 60;
        return $hour . ':' . $min;
    }
}