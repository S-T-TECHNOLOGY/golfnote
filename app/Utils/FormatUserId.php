<?php


namespace App\Utils;


class FormatUserId
{
    public static function formatUserId($userId) {
        $prefix = 'KVGA';
        if ($userId < 10) {
            return $prefix . '00000' . $userId;
        }

        if ($userId >= 10 && $userId < 100) {
            return $prefix . '0000' . $userId;
        }

        if ($userId >= 100 && $userId < 1000) {
            return $prefix . '000' . $userId;
        }

        if ($userId >= 1000 && $userId < 10000) {
            return $prefix . '00' . $userId;
        }

        if ($userId >= 10000 && $userId < 100000) {
            return $prefix . '0' . $userId;
        }

        return $prefix . $userId;
    }
}