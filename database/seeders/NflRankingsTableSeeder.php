<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NflRanking;

class NflRankingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rankings = [
            ['id' => 16, 'elo' => 1692], // KC
            ['id' => 4, 'elo' => 1690],  // BUF
            ['id' => 27, 'elo' => 1685], // SF
            ['id' => 3, 'elo' => 1667],  // BAL
            ['id' => 9, 'elo' => 1634],  // DAL
            ['id' => 11, 'elo' => 1609], // DET
            ['id' => 17, 'elo' => 1590], // MIA
            ['id' => 29, 'elo' => 1576], // LAR
            ['id' => 12, 'elo' => 1570], // GB
            ['id' => 7, 'elo' => 1552],  // CIN
            ['id' => 25, 'elo' => 1541], // PIT
            ['id' => 24, 'elo' => 1524], // PHI
            ['id' => 20, 'elo' => 1519], // NO
            ['id' => 28, 'elo' => 1518], // SEA
            ['id' => 8, 'elo' => 1496],  // CLE
            ['id' => 13, 'elo' => 1496], // HOU
            ['id' => 23, 'elo' => 1483], // LV (OAK)
            ['id' => 6, 'elo' => 1477],  // CHI
            ['id' => 15, 'elo' => 1475], // JAX
            ['id' => 30, 'elo' => 1467], // TB
            ['id' => 18, 'elo' => 1439], // MIN
            ['id' => 26, 'elo' => 1434], // LAC
            ['id' => 2, 'elo' => 1430],  // ATL
            ['id' => 10, 'elo' => 1426], // DEN
            ['id' => 14, 'elo' => 1425], // IND
            ['id' => 22, 'elo' => 1416], // NYJ
            ['id' => 21, 'elo' => 1416], // NYG
            ['id' => 31, 'elo' => 1413], // TEN
            ['id' => 1, 'elo' => 1399],  // ARI
            ['id' => 19, 'elo' => 1379], // NE
            ['id' => 32, 'elo' => 1330], // WAS
            ['id' => 5, 'elo' => 1293],  // CAR
        ];

        foreach ($rankings as $ranking) {
            NflRanking::create([
                'team_id' => $ranking['id'],
                'base_elo' => $ranking['elo'],
                'season_elo' => 1500,
                'predictive_elo' => $ranking['elo'],
                'power_ranking' => 0, // Default value, can be updated later
                'sos' => 0, // Default value, can be updated later
            ]);
        }
    }
}
