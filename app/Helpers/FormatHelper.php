<?php

namespace App\Helpers;

use App\Models\NflTeam;

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

    public static function formatOpponent($prediction, $selectedTeamId)
    {
        if ($prediction->team_id_home == $selectedTeamId) {
            $opponent = NflTeam::find($prediction->team_id_away);
            return 'vs. ' . ($opponent->name ?? 'Unknown Team');
        } else {
            $opponent = NflTeam::find($prediction->team_id_home);
            return '@ ' . ($opponent->name ?? 'Unknown Team');
        }
    }
}

