<?php


namespace App\Utils;


class FormatTime
{
    public static function convertTime($time) {
        $hour = (int) ($time / 60);
        $hour = ($hour >= 10) ? $hour : '0' . $hour;
        $min = $time % 60;
        $min = $min < 10 ? '0' . $min : $time % 60;
        return $hour . ':' . $min;
    }
}