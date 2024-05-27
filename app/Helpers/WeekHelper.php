<?php

namespace App\Helpers;

use Carbon\Carbon;

class WeekHelper
{
    public static function nflWeeks()
    {
        return [
            1 => ['start' => '2024-09-05', 'end' => '2024-09-11'],
            2 => ['start' => '2024-09-12', 'end' => '2024-09-18'],
            3 => ['start' => '2024-09-19', 'end' => '2024-09-25'],
            4 => ['start' => '2024-09-26', 'end' => '2024-10-02'],
            5 => ['start' => '2024-10-03', 'end' => '2024-10-09'],
            6 => ['start' => '2024-10-10', 'end' => '2024-10-16'],
            7 => ['start' => '2024-10-17', 'end' => '2024-10-23'],
            8 => ['start' => '2024-10-24', 'end' => '2024-10-30'],
            9 => ['start' => '2024-10-31', 'end' => '2024-11-06'],
            10 => ['start' => '2024-11-07', 'end' => '2024-11-13'],
            11 => ['start' => '2024-11-14', 'end' => '2024-11-20'],
            12 => ['start' => '2024-11-21', 'end' => '2024-11-27'],
            13 => ['start' => '2024-11-28', 'end' => '2024-12-04'],
            14 => ['start' => '2024-12-05', 'end' => '2024-12-11'],
            15 => ['start' => '2024-12-12', 'end' => '2024-12-18'],
            16 => ['start' => '2024-12-19', 'end' => '2024-12-25'],
            17 => ['start' => '2024-12-26', 'end' => '2025-01-01'],
            18 => ['start' => '2025-01-02', 'end' => '2025-01-08'],
        ];
    }

    public static function getWeeks()
    {
        return array_keys(self::nflWeeks());
    }

    public static function getWeekDates($week)
    {
        $weeks = self::nflWeeks();

        if (isset($weeks[$week])) {
            return [
                'start' => Carbon::parse($weeks[$week]['start'])->startOfDay(),
                'end' => Carbon::parse($weeks[$week]['end'])->endOfDay(),
            ];
        }

        return null;
    }

    public static function getWeek(Carbon $date, array $weeks = null)
    {
        $weeks = $weeks ?: self::nflWeeks();

        foreach ($weeks as $week => $range) {
            if ($date->between(Carbon::parse($range['start']), Carbon::parse($range['end']))) {
                return $week;
            }
        }

        return null;
    }
}
