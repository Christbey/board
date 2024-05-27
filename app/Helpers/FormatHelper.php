<?php

namespace App\Helpers;

class FormatHelper
{
    public static function formatOdds($value)
    {
        if ($value > 0) {
            return '+' . $value;
        }
        return $value;
    }
}
