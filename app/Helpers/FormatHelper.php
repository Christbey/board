<?php

namespace App\Helpers;

class FormatHelper
{
    public static function formatOdds($value, $type = 'default')
    {
        switch ($type) {
            case 'total_home':
                return 'o' . $value;
            case 'total_away':
                return 'u' . $value;
            default:
                if ($value > 0) {
                    return '+' . $value;
                }
                return $value;
        }
    }
}
