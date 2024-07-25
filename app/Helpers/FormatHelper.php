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
            $opponentTeam = NflTeam::find($prediction->team_id_away);
            return 'vs. ' . ($opponentTeam->name ?? 'Unknown Team');
        } else {
            $opponentTeam = NflTeam::find($prediction->team_id_home);
            return '@ ' . ($opponentTeam->name ?? 'Unknown Team');
        }
    }

    public static function calculateScoreBasedOnWinPercentage($homeWinPercentage, $awayWinPercentage)
    {
        $homePtsMax = config('nfl.homePtsMax');
        $awayPtsMax = config('nfl.awayPtsMax');

        $homeScore = round(($homeWinPercentage / 100) * $homePtsMax);
        $awayScore = round(($awayWinPercentage / 100) * $awayPtsMax);

        return [$homeScore, $awayScore];
    }
}
