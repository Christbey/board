<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NflEspnTeamStat extends Model
{
    use HasFactory;

    protected $table = 'nfl_espn_team_stats';

    protected $fillable = [
        'season',
        'team_id',
        'category',
        'stat_name',
        'stat_value',
        'stat_display_value',
        'stat_rank',
        'stat_rank_display_value',
    ];

    /**
     * Calculate the average rank for a team, optionally filtered by category.
     *
     * @param int $teamId
     * @param string|null $category (optional)
     * @return float|null
     */
    public function calculateAverageRank(int $teamId, int $season, ?string $category = null): ?float
    {
        return $this->where('team_id', $teamId)
            ->where('season', $season)
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->avg('stat_rank');
    }


    /**
     * Calculate the average rank for all teams in a given season, optionally filtered by category.
     *
     * @param int $season
     * @param string|null $category (optional)
     * @return array
     */
    public function calculateAverageRankForAllTeams(int $season, ?string $category = null): array
    {
        return $this->where('season', $season)
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->selectRaw('team_id, AVG(stat_rank) as avg_rank')
            ->groupBy('team_id')
            ->pluck('avg_rank', 'team_id')
            ->toArray();
    }
}
