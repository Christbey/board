<?php

namespace App\Helpers;

class NflHelper
{
    public static function getSeasonDateRange(int $year): array
    {
        // Assuming the NFL season starts in September and ends in February of the next year
        $start = date('Y-m-d', strtotime("first day of September $year"));
        $end = date('Y-m-d', strtotime('last day of February ' . ($year + 1)));

        return [$start, $end];
    }
}
