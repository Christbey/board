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

    public static function formatOpponent($opponent, $selectedTeamId)
    {
        if (!$opponent) {
            return 'Unknown Opponent';
        }

        if ($opponent->team_id_home == $selectedTeamId) {
            return 'vs. ' . ($opponent->away ?? 'Unknown Team');
        } else {
            return '@ ' . ($opponent->home ?? 'Unknown Team');
        }
    }
}
