<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MlbTeam;

class MlbTeamsTableSeeder extends Seeder
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
                'name' => 'Arizona Diamondbacks',
                'abbreviation' => 'ARI',
                'conference' => 'National',
                'division' => 'West',
                'team_mascot' => 'D-backs',
                'primary_color' => 'A71930',
                'secondary_color' => 'E3D4AD',
                'location' => 'Phoenix, AZ',
                'stadium' => 'Chase Field',
                'city' => 'Phoenix',
                'state' => 'Arizona',
            ],
            [
                'name' => 'Atlanta Braves',
                'abbreviation' => 'ATL',
                'conference' => 'National',
                'division' => 'East',
                'team_mascot' => 'Braves',
                'primary_color' => 'CE1141',
                'secondary_color' => '13274F',
                'location' => 'Cumberland, GA',
                'stadium' => 'Truist Park',
                'city' => 'Cumberland',
                'state' => 'Georgia',
            ],
            // Add all other teams similarly...
            [
                'name' => 'Boston Red Sox',
                'abbreviation' => 'BOS',
                'conference' => 'American',
                'division' => 'East',
                'team_mascot' => 'Red Sox',
                'primary_color' => 'BD3039',
                'secondary_color' => '0D2B56',
                'location' => 'Boston, MA',
                'stadium' => 'Fenway Park',
                'city' => 'Boston',
                'state' => 'Massachusetts',
            ],
            [
                'name' => 'Chicago Cubs',
                'abbreviation' => 'CHC',
                'conference' => 'National',
                'division' => 'Central',
                'team_mascot' => 'Cubs',
                'primary_color' => '0E3386',
                'secondary_color' => 'CC3433',
                'location' => 'Chicago, IL',
                'stadium' => 'Wrigley Field',
                'city' => 'Chicago',
                'state' => 'Illinois',
            ],
            [
                'name' => 'Chicago White Sox',
                'abbreviation' => 'CWS',
                'conference' => 'American',
                'division' => 'Central',
                'team_mascot' => 'White Sox',
                'primary_color' => '27251F',
                'secondary_color' => 'C4CED4',
                'location' => 'Chicago, IL',
                'stadium' => 'Guaranteed Rate Field',
                'city' => 'Chicago',
                'state' => 'Illinois',
            ],
            [
                'name' => 'Cincinnati Reds',
                'abbreviation' => 'CIN',
                'conference' => 'National',
                'division' => 'Central',
                'team_mascot' => 'Reds',
                'primary_color' => 'C6011F',
                'secondary_color' => '000000',
                'location' => 'Cincinnati, OH',
                'stadium' => 'Great American Ball Park',
                'city' => 'Cincinnati',
                'state' => 'Ohio',
            ],
            [
                'name' => 'Cleveland Guardians',
                'abbreviation' => 'CLE',
                'conference' => 'American',
                'division' => 'Central',
                'team_mascot' => 'Guardians',
                'primary_color' => '00385D',
                'secondary_color' => 'E31937',
                'location' => 'Cleveland, OH',
                'stadium' => 'Progressive Field',
                'city' => 'Cleveland',
                'state' => 'Ohio',
            ],
            [
                'name' => 'Colorado Rockies',
                'abbreviation' => 'COL',
                'conference' => 'National',
                'division' => 'West',
                'team_mascot' => 'Rockies',
                'primary_color' => '33006F',
                'secondary_color' => '000000',
                'location' => 'Denver, CO',
                'stadium' => 'Coors Field',
                'city' => 'Denver',
                'state' => 'Colorado',
            ],
            [
                'name' => 'Detroit Tigers',
                'abbreviation' => 'DET',
                'conference' => 'American',
                'division' => 'Central',
                'team_mascot' => 'Tigers',
                'primary_color' => '0C2340',
                'secondary_color' => 'FA4616',
                'location' => 'Detroit, MI',
                'stadium' => 'Comerica Park',
                'city' => 'Detroit',
                'state' => 'Michigan',
            ],
            [
                'name' => 'Houston Astros',
                'abbreviation' => 'HOU',
                'conference' => 'American',
                'division' => 'West',
                'team_mascot' => 'Astros',
                'primary_color' => '002D62',
                'secondary_color' => 'EB6E1F',
                'location' => 'Houston, TX',
                'stadium' => 'Minute Maid Park',
                'city' => 'Houston',
                'state' => 'Texas',
            ],
            [
                'name' => 'Kansas City Royals',
                'abbreviation' => 'KC',
                'conference' => 'American',
                'division' => 'Central',
                'team_mascot' => 'Royals',
                'primary_color' => '004687',
                'secondary_color' => 'BD9B60',
                'location' => 'Kansas City, MO',
                'stadium' => 'Kauffman Stadium',
                'city' => 'Kansas City',
                'state' => 'Missouri',
            ],
            [
                'name' => 'Los Angeles Angels',
                'abbreviation' => 'LAA',
                'conference' => 'American',
                'division' => 'West',
                'team_mascot' => 'Angels',
                'primary_color' => 'BA0021',
                'secondary_color' => '003263',
                'location' => 'Anaheim, CA',
                'stadium' => 'Angel Stadium',
                'city' => 'Anaheim',
                'state' => 'California',
            ],
            [
                'name' => 'Los Angeles Dodgers',
                'abbreviation' => 'LAD',
                'conference' => 'National',
                'division' => 'West',
                'team_mascot' => 'Dodgers',
                'primary_color' => '005A9C',
                'secondary_color' => 'EF3E42',
                'location' => 'Los Angeles, CA',
                'stadium' => 'Dodger Stadium',
                'city' => 'Los Angeles',
                'state' => 'California',
            ],
            [
                'name' => 'Miami Marlins',
                'abbreviation' => 'MIA',
                'conference' => 'National',
                'division' => 'East',
                'team_mascot' => 'Marlins',
                'primary_color' => '00A3E0',
                'secondary_color' => 'F9423A',
                'location' => 'Miami, FL',
                'stadium' => 'loanDepot Park',
                'city' => 'Miami',
                'state' => 'Florida',
            ],
            [
                'name' => 'Milwaukee Brewers',
                'abbreviation' => 'MIL',
                'conference' => 'National',
                'division' => 'Central',
                'team_mascot' => 'Brewers',
                'primary_color' => 'FFC52F',
                'secondary_color' => '12284B',
                'location' => 'Milwaukee, WI',
                'stadium' => 'American Family Field',
                'city' => 'Milwaukee',
                'state' => 'Wisconsin',
            ],
            [
                'name' => 'Minnesota Twins',
                'abbreviation' => 'MIN',
                'conference' => 'American',
                'division' => 'Central',
                'team_mascot' => 'Twins',
                'primary_color' => '002B5C',
                'secondary_color' => 'D31145',
                'location' => 'Minneapolis, MN',
                'stadium' => 'Target Field',
                'city' => 'Minneapolis',
                'state' => 'Minnesota',
            ],
            [
                'name' => 'New York Mets',
                'abbreviation' => 'NYM',
                'conference' => 'National',
                'division' => 'East',
                'team_mascot' => 'Mets',
                'primary_color' => '002B5C',
                'secondary_color' => 'D31145',
                'location' => 'Minneapolis, MN',
                'stadium' => 'Target Field',
                'city' => 'Minneapolis',
                'state' => 'Minnesota',
            ],
            [
                'name' => 'New York Yankees',
                'abbreviation' => 'NYY',
                'conference' => 'American',
                'division' => 'East',
                'team_mascot' => 'Yankees',
                'primary_color' => '003087',
                'secondary_color' => 'E4002B',
                'location' => 'New York, NY',
                'stadium' => 'Yankee Stadium',
                'city' => 'New York',
                'state' => 'New York',
            ],
            [
                'name' => 'Oakland Athletics',
                'abbreviation' => 'OAK',
                'conference' => 'American',
                'division' => 'West',
                'team_mascot' => 'Athletics',
                'primary_color' => '003831',
                'secondary_color' => 'EFB21E',
                'location' => 'Oakland, CA',
                'stadium' => 'RingCentral Coliseum',
                'city' => 'Oakland',
                'state' => 'California',
            ],
            [
                'name' => 'Philadelphia Phillies',
                'abbreviation' => 'PHI',
                'conference' => 'National',
                'division' => 'East',
                'team_mascot' => 'Phillies',
                'primary_color' => 'E81828',
                'secondary_color' => '002D72',
                'location' => 'Philadelphia, PA',
                'stadium' => 'Citizens Bank Park',
                'city' => 'Philadelphia',
                'state' => 'Pennsylvania',
            ],
            [
                'name' => 'Pittsburgh Pirates',
                'abbreviation' => 'PIT',
                'conference' => 'National',
                'division' => 'Central',
                'team_mascot' => 'Pirates',
                'primary_color' => 'FDB827',
                'secondary_color' => '000000',
                'location' => 'Pittsburgh, PA',
                'stadium' => 'PNC Park',
                'city' => 'Pittsburgh',
                'state' => 'Pennsylvania',
            ],
            [
                'name' => 'San Diego Padres',
                'abbreviation' => 'SD',
                'conference' => 'National',
                'division' => 'West',
                'team_mascot' => 'Padres',
                'primary_color' => '2F241D',
                'secondary_color' => 'FFC72C',
                'location' => 'San Diego, CA',
                'stadium' => 'Petco Park',
                'city' => 'San Diego',
                'state' => 'California',
            ],
            [
                'name' => 'San Francisco Giants',
                'abbreviation' => 'SF',
                'conference' => 'National',
                'division' => 'West',
                'team_mascot' => 'Giants',
                'primary_color' => 'FD5A1E',
                'secondary_color' => '27251F',
                'location' => 'San Francisco, CA',
                'stadium' => 'Oracle Park',
                'city' => 'San Francisco',
                'state' => 'California',
            ],
            [
                'name' => 'Seattle Mariners',
                'abbreviation' => 'SEA',
                'conference' => 'American',
                'division' => 'West',
                'team_mascot' => 'Mariners',
                'primary_color' => '0C2C56',
                'secondary_color' => '005C5C',
                'location' => 'Seattle, WA',
                'stadium' => 'T-Mobile Park',
                'city' => 'Seattle',
                'state' => 'Washington',
            ],
            [
                'name' => 'St. Louis Cardinals',
                'abbreviation' => 'STL',
                'conference' => 'National',
                'division' => 'Central',
                'team_mascot' => 'Cardinals',
                'primary_color' => 'C41E3A',
                'secondary_color' => '0A2252',
                'location' => 'St. Louis, MO',
                'stadium' => 'Busch Stadium',
                'city' => 'St. Louis',
                'state' => 'Missouri',
            ],
            [
                'name' => 'Tampa Bay Rays',
                'abbreviation' => 'TB',
                'conference' => 'American',
                'division' => 'East',
                'team_mascot' => 'Rays',
                'primary_color' => '092C5C',
                'secondary_color' => '8FBCE6',
                'location' => 'St. Petersburg, FL',
                'stadium' => 'Tropicana Field',
                'city' => 'St. Petersburg',
                'state' => 'Florida',
            ],
            [
                'name' => 'Texas Rangers',
                'abbreviation' => 'TEX',
                'conference' => 'American',
                'division' => 'West',
                'team_mascot' => 'Rangers',
                'primary_color' => '003278',
                'secondary_color' => 'C0111F',
                'location' => 'Arlington, TX',
                'stadium' => 'Globe Life Field',
                'city' => 'Arlington',
                'state' => 'Texas',
            ],
            [
                'name' => 'Toronto Blue Jays',
                'abbreviation' => 'TOR',
                'conference' => 'American',
                'division' => 'East',
                'team_mascot' => 'Blue Jays',
                'primary_color' => '134A8E',
                'secondary_color' => '1D2D5C',
                'location' => 'Toronto, ON',
                'stadium' => 'Rogers Centre',
                'city' => 'Toronto',
                'state' => 'Ontario',
            ],
            [
                'name' => 'Washington Nationals',
                'abbreviation' => 'WSH',
                'conference' => 'National',
                'division' => 'East',
                'team_mascot' => 'Nationals',
                'primary_color' => 'AB0003',
                'secondary_color' => '14225A',
                'location' => 'Washington, D.C.',
                'stadium' => 'Nationals Park',
                'city' => 'Washington',
                'state' => 'District of Columbia',
            ],
        ];

        foreach ($teams as $team) {
            MlbTeam::create($team);
        }
    }
}
