<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NflTeam;

class NflTeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $teams = [
            [
                'name' => 'Arizona Cardinals',
                'abbreviation' => 'ARI',
                'conference' => 'NFC',
                'division' => 'West',
                'team_mascot' => 'Cardinals',
                'primary_color' => '#97233F',
                'secondary_color' => '#000000',
                'location' => 'Glendale',
                'stadium' => 'State Farm Stadium',
                'city' => 'Phoenix',
                'state' => 'Arizona',
            ],
            [
                'name' => 'Atlanta Falcons',
                'abbreviation' => 'ATL',
                'conference' => 'NFC',
                'division' => 'South',
                'team_mascot' => 'Falcons',
                'primary_color' => '#A71930',
                'secondary_color' => '#000000',
                'location' => 'Atlanta',
                'stadium' => 'Mercedes-Benz Stadium',
                'city' => 'Atlanta',
                'state' => 'Georgia',
            ],
            [
                'name' => 'Baltimore Ravens',
                'abbreviation' => 'BAL',
                'conference' => 'AFC',
                'division' => 'North',
                'team_mascot' => 'Ravens',
                'primary_color' => '#241773',
                'secondary_color' => '#9E7C0C',
                'location' => 'Baltimore',
                'stadium' => 'M&T Bank Stadium',
                'city' => 'Baltimore',
                'state' => 'Maryland',
            ],
            [
                'name' => 'Buffalo Bills',
                'abbreviation' => 'BUF',
                'conference' => 'AFC',
                'division' => 'East',
                'team_mascot' => 'Bills',
                'primary_color' => '#00338D',
                'secondary_color' => '#C60C30',
                'location' => 'Orchard Park',
                'stadium' => 'Highmark Stadium',
                'city' => 'Buffalo',
                'state' => 'New York',
            ],
            [
                'name' => 'Carolina Panthers',
                'abbreviation' => 'CAR',
                'conference' => 'NFC',
                'division' => 'South',
                'team_mascot' => 'Panthers',
                'primary_color' => '#0085CA',
                'secondary_color' => '#000000',
                'location' => 'Charlotte',
                'stadium' => 'Bank of America Stadium',
                'city' => 'Charlotte',
                'state' => 'North Carolina',
            ],
            [
                'name' => 'Chicago Bears',
                'abbreviation' => 'CHI',
                'conference' => 'NFC',
                'division' => 'North',
                'team_mascot' => 'Bears',
                'primary_color' => '#0B162A',
                'secondary_color' => '#E64100',
                'location' => 'Chicago',
                'stadium' => 'Soldier Field',
                'city' => 'Chicago',
                'state' => 'Illinois',
            ],
            [
                'name' => 'Cincinnati Bengals',
                'abbreviation' => 'CIN',
                'conference' => 'AFC',
                'division' => 'North',
                'team_mascot' => 'Bengals',
                'primary_color' => '#FB4F14',
                'secondary_color' => '#000000',
                'location' => 'Cincinnati',
                'stadium' => 'Paul Brown Stadium',
                'city' => 'Cincinnati',
                'state' => 'Ohio',
            ],
            [
                'name' => 'Cleveland Browns',
                'abbreviation' => 'CLE',
                'conference' => 'AFC',
                'division' => 'North',
                'team_mascot' => 'Browns',
                'primary_color' => '#FF3C00',
                'secondary_color' => '#311D00',
                'location' => 'Cleveland',
                'stadium' => 'FirstEnergy Stadium',
                'city' => 'Cleveland',
                'state' => 'Ohio',
            ],
            [
                'name' => 'Dallas Cowboys',
                'abbreviation' => 'DAL',
                'conference' => 'NFC',
                'division' => 'East',
                'team_mascot' => 'Cowboys',
                'primary_color' => '#002244',
                'secondary_color' => '#B0B7BC',
                'location' => 'Arlington',
                'stadium' => 'AT&T Stadium',
                'city' => 'Dallas',
                'state' => 'Texas',
            ],
            [
                'name' => 'Denver Broncos',
                'abbreviation' => 'DEN',
                'conference' => 'AFC',
                'division' => 'West',
                'team_mascot' => 'Broncos',
                'primary_color' => '#002244',
                'secondary_color' => '#FB4F14',
                'location' => 'Denver',
                'stadium' => 'Empower Field at Mile High',
                'city' => 'Denver',
                'state' => 'Colorado',
            ],
            [
                'name' => 'Detroit Lions',
                'abbreviation' => 'DET',
                'conference' => 'NFC',
                'division' => 'North',
                'team_mascot' => 'Lions',
                'primary_color' => '#0076B6',
                'secondary_color' => '#B0B7BC',
                'location' => 'Detroit',
                'stadium' => 'Ford Field',
                'city' => 'Detroit',
                'state' => 'Michigan',
            ],
            [
                'name' => 'Green Bay Packers',
                'abbreviation' => 'GB',
                'conference' => 'NFC',
                'division' => 'North',
                'team_mascot' => 'Packers',
                'primary_color' => '#203731',
                'secondary_color' => '#FFB612',
                'location' => 'Green Bay',
                'stadium' => 'Lambeau Field',
                'city' => 'Green Bay',
                'state' => 'Wisconsin',
            ],
            [
                'name' => 'Houston Texans',
                'abbreviation' => 'HOU',
                'conference' => 'AFC',
                'division' => 'South',
                'team_mascot' => 'Texans',
                'primary_color' => '#03202F',
                'secondary_color' => '#A71930',
                'location' => 'Houston',
                'stadium' => 'NRG Stadium',
                'city' => 'Houston',
                'state' => 'Texas',
            ],
            [
                'name' => 'Indianapolis Colts',
                'abbreviation' => 'IND',
                'conference' => 'AFC',
                'division' => 'South',
                'team_mascot' => 'Colts',
                'primary_color' => '#002C5F',
                'secondary_color' => '#A5ACAF',
                'location' => 'Indianapolis',
                'stadium' => 'Lucas Oil Stadium',
                'city' => 'Indianapolis',
                'state' => 'Indiana',
            ],
            [
                'name' => 'Jacksonville Jaguars',
                'abbreviation' => 'JAX',
                'conference' => 'AFC',
                'division' => 'South',
                'team_mascot' => 'Jaguars',
                'primary_color' => '#006778',
                'secondary_color' => '#000000',
                'location' => 'Jacksonville',
                'stadium' => 'TIAA Bank Field',
                'city' => 'Jacksonville',
                'state' => 'Florida',
            ],
            [
                'name' => 'Kansas City Chiefs',
                'abbreviation' => 'KC',
                'conference' => 'AFC',
                'division' => 'West',
                'team_mascot' => 'Chiefs',
                'primary_color' => '#E31837',
                'secondary_color' => '#FFB612',
                'location' => 'Kansas City',
                'stadium' => 'Arrowhead Stadium',
                'city' => 'Kansas City',
                'state' => 'Missouri',
            ],
            [
                'name' => 'Miami Dolphins',
                'abbreviation' => 'MIA',
                'conference' => 'AFC',
                'division' => 'East',
                'team_mascot' => 'Dolphins',
                'primary_color' => '#008E97',
                'secondary_color' => '#F58220',
                'location' => 'Miami Gardens',
                'stadium' => 'Hard Rock Stadium',
                'city' => 'Miami',
                'state' => 'Florida',
            ],
            [
                'name' => 'Minnesota Vikings',
                'abbreviation' => 'MIN',
                'conference' => 'NFC',
                'division' => 'North',
                'team_mascot' => 'Vikings',
                'primary_color' => '#4F2683',
                'secondary_color' => '#FFC62F',
                'location' => 'Minneapolis',
                'stadium' => 'U.S. Bank Stadium',
                'city' => 'Minneapolis',
                'state' => 'Minnesota',
            ],
            [
                'name' => 'New England Patriots',
                'abbreviation' => 'NE',
                'conference' => 'AFC',
                'division' => 'East',
                'team_mascot' => 'Patriots',
                'primary_color' => '#002244',
                'secondary_color' => '#C60C30',
                'location' => 'Foxborough',
                'stadium' => 'Gillette Stadium',
                'city' => 'New England',
                'state' => 'Massachusetts',
            ],
            [
                'name' => 'New Orleans Saints',
                'abbreviation' => 'NO',
                'conference' => 'NFC',
                'division' => 'South',
                'team_mascot' => 'Saints',
                'primary_color' => '#D3BC8D',
                'secondary_color' => '#000000',
                'location' => 'New Orleans',
                'stadium' => 'Caesars Superdome',
                'city' => 'New Orleans',
                'state' => 'Louisiana',
            ],
            [
                'name' => 'New York Giants',
                'abbreviation' => 'NYG',
                'conference' => 'NFC',
                'division' => 'East',
                'team_mascot' => 'Giants',
                'primary_color' => '#0B2265',
                'secondary_color' => '#A71930',
                'location' => 'East Rutherford',
                'stadium' => 'MetLife Stadium',
                'city' => 'New York',
                'state' => 'New Jersey',
            ],
            [
                'name' => 'New York Jets',
                'abbreviation' => 'NYJ',
                'conference' => 'AFC',
                'division' => 'East',
                'team_mascot' => 'Jets',
                'primary_color' => '#003F2D',
                'secondary_color' => '#000000',
                'location' => 'East Rutherford',
                'stadium' => 'MetLife Stadium',
                'city' => 'New York',
                'state' => 'New Jersey',
            ],
            [
                'name' => 'Las Vegas Raiders',
                'abbreviation' => 'LV',
                'conference' => 'AFC',
                'division' => 'West',
                'team_mascot' => 'Raiders',
                'primary_color' => '#000000',
                'secondary_color' => '#A5ACAF',
                'location' => 'Las Vegas',
                'stadium' => 'Allegiant Stadium',
                'city' => 'Las Vegas',
                'state' => 'Nevada',
            ],
            [
                'name' => 'Philadelphia Eagles',
                'abbreviation' => 'PHI',
                'conference' => 'NFC',
                'division' => 'East',
                'team_mascot' => 'Eagles',
                'primary_color' => '#004C54',
                'secondary_color' => '#A5ACAF',
                'location' => 'Philadelphia',
                'stadium' => 'Lincoln Financial Field',
                'city' => 'Philadelphia',
                'state' => 'Pennsylvania',
            ],
            [
                'name' => 'Pittsburgh Steelers',
                'abbreviation' => 'PIT',
                'conference' => 'AFC',
                'division' => 'North',
                'team_mascot' => 'Steelers',
                'primary_color' => '#000000',
                'secondary_color' => '#FFB612',
                'location' => 'Pittsburgh',
                'stadium' => 'Heinz Field',
                'city' => 'Pittsburgh',
                'state' => 'Pennsylvania',
            ],
            [
                'name' => 'Los Angeles Chargers',
                'abbreviation' => 'LAC',
                'conference' => 'AFC',
                'division' => 'West',
                'team_mascot' => 'Chargers',
                'primary_color' => '#007BC7',
                'secondary_color' => '#FFC20E',
                'location' => 'Inglewood',
                'stadium' => 'SoFi Stadium',
                'city' => 'Los Angeles',
                'state' => 'California',
            ],
            [
                'name' => 'San Francisco 49ers',
                'abbreviation' => 'SF',
                'conference' => 'NFC',
                'division' => 'West',
                'team_mascot' => '49ers',
                'primary_color' => '#AA0000',
                'secondary_color' => '#B3995D',
                'location' => 'Santa Clara',
                'stadium' => 'Levi\'s Stadium',
                'city' => 'San Francisco',
                'state' => 'California',
            ],
            [
                'name' => 'Seattle Seahawks',
                'abbreviation' => 'SEA',
                'conference' => 'NFC',
                'division' => 'West',
                'team_mascot' => 'Seahawks',
                'primary_color' => '#002244',
                'secondary_color' => '#69BE28',
                'location' => 'Seattle',
                'stadium' => 'Lumen Field',
                'city' => 'Seattle',
                'state' => 'Washington',
            ],
            [
                'name' => 'Los Angeles Rams',
                'abbreviation' => 'LAR',
                'conference' => 'NFC',
                'division' => 'West',
                'team_mascot' => 'Rams',
                'primary_color' => '#003594',
                'secondary_color' => '#FFD100',
                'location' => 'Inglewood',
                'stadium' => 'SoFi Stadium',
                'city' => 'Los Angeles',
                'state' => 'California',
            ],
            [
                'name' => 'Tampa Bay Buccaneers',
                'abbreviation' => 'TB',
                'conference' => 'NFC',
                'division' => 'South',
                'team_mascot' => 'Buccaneers',
                'primary_color' => '#A71930',
                'secondary_color' => '#322F2B',
                'location' => 'Tampa',
                'stadium' => 'Raymond James Stadium',
                'city' => 'Tampa',
                'state' => 'Florida',
            ],
            [
                'name' => 'Tennessee Titans',
                'abbreviation' => 'TEN',
                'conference' => 'AFC',
                'division' => 'South',
                'team_mascot' => 'Titans',
                'primary_color' => '#002244',
                'secondary_color' => '#4B92DB',
                'location' => 'Nashville',
                'stadium' => 'Nissan Stadium',
                'city' => 'Nashville',
                'state' => 'Tennessee',
            ],
            [
                'name' => 'Washington Commanders',
                'abbreviation' => 'WAS',
                'conference' => 'NFC',
                'division' => 'East',
                'team_mascot' => 'Commanders',
                'primary_color' => '#5A1414',
                'secondary_color' => '#FFB612',
                'location' => 'Landover',
                'stadium' => 'FedEx Field',
                'city' => 'Washington',
                'state' => 'D.C.',
            ],
        ];

        foreach ($teams as $team) {
            NflTeam::create($team);
        }
    }
}