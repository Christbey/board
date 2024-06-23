<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NflStadiumsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $now = Carbon::now();

        $stadiums = [
            ['stadium_name' => 'Alamodome', 'team_id' => $this->getTeamId('NO'), 'roof_type' => 'Indoor', 'longitude' => -98.478889, 'latitude' => 29.416944, 'stadium_azimuth_angle' => 0, 'city' => 'San Antonio', 'state' => 'TX'],
            ['stadium_name' => 'Allegiant Stadium', 'team_id' => $this->getTeamId('LV'), 'roof_type' => 'Indoor', 'longitude' => -115.183722, 'latitude' => 36.09075, 'stadium_azimuth_angle' => 0, 'city' => 'Las Vegas', 'state' => 'NV'],
            ['stadium_name' => 'Arrowhead Stadium', 'team_id' => $this->getTeamId('KC'), 'roof_type' => 'Outdoor', 'longitude' => -94.483889, 'latitude' => 39.048889, 'stadium_azimuth_angle' => 316.3, 'city' => 'Kansas City', 'state' => 'MO'],
            ['stadium_name' => 'AT&T Stadium', 'team_id' => $this->getTeamId('DAL'), 'roof_type' => 'Retractable', 'longitude' => -97.092778, 'latitude' => 32.747778, 'stadium_azimuth_angle' => 68, 'city' => 'Arlington', 'state' => 'TX'],
            ['stadium_name' => 'Bank of America Stadium', 'team_id' => $this->getTeamId('CAR'), 'roof_type' => 'Outdoor', 'longitude' => -80.852778, 'latitude' => 35.225833, 'stadium_azimuth_angle' => 322.4, 'city' => 'Charlotte', 'state' => 'NC'],
            ['stadium_name' => 'Candlestick Park', 'team_id' => $this->getTeamId('SF'), 'roof_type' => 'Outdoor', 'longitude' => -122.386111, 'latitude' => 37.713611, 'stadium_azimuth_angle' => 0, 'city' => 'San Francisco', 'state' => 'CA'],
            ['stadium_name' => 'CenturyLink Field', 'team_id' => $this->getTeamId('SEA'), 'roof_type' => 'Outdoor', 'longitude' => -122.3316, 'latitude' => 47.5952, 'stadium_azimuth_angle' => 0, 'city' => 'Seattle', 'state' => 'WA'],
            ['stadium_name' => 'Dignity Health Sports Park', 'team_id' => $this->getTeamId('LAC'), 'roof_type' => 'Outdoor', 'longitude' => -118.261, 'latitude' => 33.864, 'stadium_azimuth_angle' => 0, 'city' => 'Carson', 'state' => 'CA'],
            ['stadium_name' => 'Empower Field at Mile High', 'team_id' => $this->getTeamId('DEN'), 'roof_type' => 'Outdoor', 'longitude' => -105.02, 'latitude' => 39.743889, 'stadium_azimuth_angle' => 0, 'city' => 'Denver', 'state' => 'CO'],
            ['stadium_name' => 'Estadio Azteca', 'team_id' => $this->getTeamId(''), 'roof_type' => 'Outdoor', 'longitude' => -99.150442, 'latitude' => 19.302911, 'stadium_azimuth_angle' => 5.9, 'city' => 'Mexico City', 'state' => 'Mexico'],
            ['stadium_name' => 'FedExField', 'team_id' => $this->getTeamId('WAS'), 'roof_type' => 'Outdoor', 'longitude' => -76.864444, 'latitude' => 38.907778, 'stadium_azimuth_angle' => 295, 'city' => 'Landover', 'state' => 'MD'],
            ['stadium_name' => 'FirstEnergy Stadium', 'team_id' => $this->getTeamId('CLE'), 'roof_type' => 'Outdoor', 'longitude' => -81.699444, 'latitude' => 41.506111, 'stadium_azimuth_angle' => 56.1, 'city' => 'Cleveland', 'state' => 'OH'],
            ['stadium_name' => 'Ford Field', 'team_id' => $this->getTeamId('DET'), 'roof_type' => 'Indoor', 'longitude' => -83.045556, 'latitude' => 42.34, 'stadium_azimuth_angle' => 63.7, 'city' => 'Detroit', 'state' => 'MI'],
            ['stadium_name' => 'Foxboro Stadium', 'team_id' => $this->getTeamId('NE'), 'roof_type' => 'Outdoor', 'longitude' => -71.267442, 'latitude' => 42.0927, 'stadium_azimuth_angle' => 353.7, 'city' => 'Foxborough', 'state' => 'MA'],
            ['stadium_name' => 'Georgia Dome', 'team_id' => $this->getTeamId('ATL'), 'roof_type' => 'Indoor', 'longitude' => -84.401, 'latitude' => 33.758, 'stadium_azimuth_angle' => 90, 'city' => 'Atlanta', 'state' => 'GA'],
            ['stadium_name' => 'Giants Stadium', 'team_id' => $this->getTeamId('NYG'), 'roof_type' => 'Outdoor', 'longitude' => -74.076944, 'latitude' => 40.812222, 'stadium_azimuth_angle' => 308, 'city' => 'East Rutherford', 'state' => 'NJ'],
            ['stadium_name' => 'Giants Stadium', 'team_id' => $this->getTeamId('NYJ'), 'roof_type' => 'Outdoor', 'longitude' => -74.076944, 'latitude' => 40.812222, 'stadium_azimuth_angle' => 308, 'city' => 'East Rutherford', 'state' => 'NJ'],
            ['stadium_name' => 'Gillette Stadium', 'team_id' => $this->getTeamId('NE'), 'roof_type' => 'Outdoor', 'longitude' => -71.264, 'latitude' => 42.091, 'stadium_azimuth_angle' => 343, 'city' => 'Foxborough', 'state' => 'MA'],
            ['stadium_name' => 'Hard Rock Stadium', 'team_id' => $this->getTeamId('MIA'), 'roof_type' => 'Outdoor', 'longitude' => -80.238889, 'latitude' => 25.958056, 'stadium_azimuth_angle' => 302.1, 'city' => 'Miami Gardens', 'state' => 'FL'],
            ['stadium_name' => 'Heinz Field', 'team_id' => $this->getTeamId('PIT'), 'roof_type' => 'Outdoor', 'longitude' => -80.015833, 'latitude' => 40.446667, 'stadium_azimuth_angle' => 333.9, 'city' => 'Pittsburgh', 'state' => 'PA'],
            ['stadium_name' => 'Husky Stadium', 'team_id' => $this->getTeamId('SEA'), 'roof_type' => 'Outdoor', 'longitude' => -122.301667, 'latitude' => 47.650278, 'stadium_azimuth_angle' => 289.6, 'city' => 'Seattle', 'state' => 'WA'],
            ['stadium_name' => 'Lambeau Field', 'team_id' => $this->getTeamId('GB'), 'roof_type' => 'Outdoor', 'longitude' => -88.062222, 'latitude' => 44.501389, 'stadium_azimuth_angle' => 0, 'city' => 'Green Bay', 'state' => 'WI'],
            ['stadium_name' => 'Levi\'s Stadium', 'team_id' => $this->getTeamId('SF'), 'roof_type' => 'Outdoor', 'longitude' => -121.97, 'latitude' => 37.403, 'stadium_azimuth_angle' => 330, 'city' => 'Santa Clara', 'state' => 'CA'],
            ['stadium_name' => 'Lincoln Financial Field', 'team_id' => $this->getTeamId('PHI'), 'roof_type' => 'Outdoor', 'longitude' => -75.1675, 'latitude' => 39.900833, 'stadium_azimuth_angle' => 351, 'city' => 'Philadelphia', 'state' => 'PA'],
            ['stadium_name' => 'Los Angeles Memorial Coliseum', 'team_id' => $this->getTeamId('LAR'), 'roof_type' => 'Outdoor', 'longitude' => -118.287778, 'latitude' => 34.014167, 'stadium_azimuth_angle' => 90, 'city' => 'Los Angeles', 'state' => 'CA'],
            ['stadium_name' => 'Lucas Oil Stadium', 'team_id' => $this->getTeamId('IND'), 'roof_type' => 'Retractable', 'longitude' => -86.162806, 'latitude' => 39.760056, 'stadium_azimuth_angle' => 26.6, 'city' => 'Indianapolis', 'state' => 'IN'],
            ['stadium_name' => 'M&T Bank Stadium', 'team_id' => $this->getTeamId('BAL'), 'roof_type' => 'Outdoor', 'longitude' => -76.622778, 'latitude' => 39.278056, 'stadium_azimuth_angle' => 289.5, 'city' => 'Baltimore', 'state' => 'MD'],
            ['stadium_name' => 'Memorial Stadium', 'team_id' => $this->getTeamId('CHI'), 'roof_type' => 'Outdoor', 'longitude' => -88.23583333, 'latitude' => 40.09916667, 'stadium_azimuth_angle' => 0, 'city' => 'Champaign', 'state' => 'IL'],
            ['stadium_name' => 'Mercedes-Benz Stadium', 'team_id' => $this->getTeamId('ATL'), 'roof_type' => 'Retractable', 'longitude' => -84.4, 'latitude' => 33.755556, 'stadium_azimuth_angle' => 70.9, 'city' => 'Atlanta', 'state' => 'GA'],
            ['stadium_name' => 'Mercedes-Benz Superdome', 'team_id' => $this->getTeamId('NO'), 'roof_type' => 'Indoor', 'longitude' => -90.811111, 'latitude' => 29.950833, 'stadium_azimuth_angle' => 30, 'city' => 'New Orleans', 'state' => 'LA'],
            ['stadium_name' => 'MetLife Stadium', 'team_id' => $this->getTeamId('NYG'), 'roof_type' => 'Outdoor', 'longitude' => -74.074361, 'latitude' => 40.813528, 'stadium_azimuth_angle' => 345.5, 'city' => 'East Rutherford', 'state' => 'NJ'],
            ['stadium_name' => 'MetLife Stadium', 'team_id' => $this->getTeamId('NYJ'), 'roof_type' => 'Outdoor', 'longitude' => -74.074361, 'latitude' => 40.813528, 'stadium_azimuth_angle' => 345.5, 'city' => 'East Rutherford', 'state' => 'NJ'],
            ['stadium_name' => 'Mile High Stadium', 'team_id' => $this->getTeamId('DEN'), 'roof_type' => 'Outdoor', 'longitude' => -105.0216667, 'latitude' => 39.74611111, 'stadium_azimuth_angle' => 0, 'city' => 'Denver', 'state' => 'CO'],
            ['stadium_name' => 'New Era Field', 'team_id' => $this->getTeamId('BUF'), 'roof_type' => 'Outdoor', 'longitude' => -78.787, 'latitude' => 42.774, 'stadium_azimuth_angle' => 302.1, 'city' => 'Orchard Park', 'state' => 'NY'],
            ['stadium_name' => 'Nissan Stadium', 'team_id' => $this->getTeamId('TEN'), 'roof_type' => 'Outdoor', 'longitude' => -86.771389, 'latitude' => 36.166389, 'stadium_azimuth_angle' => 334.6, 'city' => 'Nashville', 'state' => 'TN'],
            ['stadium_name' => 'NRG Stadium', 'team_id' => $this->getTeamId('HOU'), 'roof_type' => 'Retractable', 'longitude' => -95.410833, 'latitude' => 29.684722, 'stadium_azimuth_angle' => 358.2, 'city' => 'Houston', 'state' => 'TX'],
            ['stadium_name' => 'Oakland Coliseum', 'team_id' => $this->getTeamId('OAK'), 'roof_type' => 'Outdoor', 'longitude' => -122.200556, 'latitude' => 37.751667, 'stadium_azimuth_angle' => 324.7, 'city' => 'Oakland', 'state' => 'CA'],
            ['stadium_name' => 'Paul Brown Stadium', 'team_id' => $this->getTeamId('CIN'), 'roof_type' => 'Outdoor', 'longitude' => -84.516, 'latitude' => 39.095, 'stadium_azimuth_angle' => 320.6, 'city' => 'Cincinnati', 'state' => 'OH'],
            ['stadium_name' => 'Pontiac Silverdome', 'team_id' => $this->getTeamId('DET'), 'roof_type' => 'Indoor', 'longitude' => -83.255, 'latitude' => 42.64583333, 'stadium_azimuth_angle' => 310, 'city' => 'Pontiac', 'state' => 'MI'],
            ['stadium_name' => 'Qualcomm Stadium', 'team_id' => $this->getTeamId('SD'), 'roof_type' => 'Outdoor', 'longitude' => -117.119444, 'latitude' => 32.783056, 'stadium_azimuth_angle' => 291.6, 'city' => 'San Diego', 'state' => 'CA'],
            ['stadium_name' => 'Raymond James Stadium', 'team_id' => $this->getTeamId('TB'), 'roof_type' => 'Outdoor', 'longitude' => -82.503333, 'latitude' => 27.975833, 'stadium_azimuth_angle' => 0, 'city' => 'Tampa', 'state' => 'FL'],
            ['stadium_name' => 'RCA Dome', 'team_id' => $this->getTeamId('IND'), 'roof_type' => 'Indoor', 'longitude' => -86.16333333, 'latitude' => 39.76361111, 'stadium_azimuth_angle' => 90, 'city' => 'Indianapolis', 'state' => 'IN'],
            ['stadium_name' => 'Rogers Centre', 'team_id' => $this->getTeamId('Toronto'), 'roof_type' => 'Retractable', 'longitude' => -79.389167, 'latitude' => 43.641389, 'stadium_azimuth_angle' => 346.4, 'city' => 'Toronto', 'state' => 'ON'],
            ['stadium_name' => 'SoFi Stadium', 'team_id' => $this->getTeamId('LAC'), 'roof_type' => 'Outdoor', 'longitude' => -118.3392, 'latitude' => 33.95345, 'stadium_azimuth_angle' => 338.2, 'city' => 'Inglewood', 'state' => 'CA'],
            ['stadium_name' => 'SoFi Stadium', 'team_id' => $this->getTeamId('LAR'), 'roof_type' => 'Outdoor', 'longitude' => -118.3392, 'latitude' => 33.95345, 'stadium_azimuth_angle' => 338.2, 'city' => 'Inglewood', 'state' => 'CA'],
            ['stadium_name' => 'Soldier Field', 'team_id' => $this->getTeamId('CHI'), 'roof_type' => 'Outdoor', 'longitude' => -87.6167, 'latitude' => 41.8623, 'stadium_azimuth_angle' => 353.9, 'city' => 'Chicago', 'state' => 'IL'],
            ['stadium_name' => 'State Farm Stadium', 'team_id' => $this->getTeamId('ARI'), 'roof_type' => 'Retractable', 'longitude' => -112.263, 'latitude' => 33.528, 'stadium_azimuth_angle' => 330, 'city' => 'Glendale', 'state' => 'AZ'],
            ['stadium_name' => 'Sun Devil Stadium', 'team_id' => $this->getTeamId('ARI'), 'roof_type' => 'Outdoor', 'longitude' => -111.9325, 'latitude' => 33.42638889, 'stadium_azimuth_angle' => 14.2, 'city' => 'Tempe', 'state' => 'AZ'],
            ['stadium_name' => 'TCF Bank Stadium', 'team_id' => $this->getTeamId('MIN'), 'roof_type' => 'Outdoor', 'longitude' => -93.225, 'latitude' => 44.976, 'stadium_azimuth_angle' => 90, 'city' => 'Minneapolis', 'state' => 'MN'],
            ['stadium_name' => 'Texas Stadium', 'team_id' => $this->getTeamId('DAL'), 'roof_type' => 'Indoor', 'longitude' => -96.911, 'latitude' => 32.84, 'stadium_azimuth_angle' => 39.4, 'city' => 'Irving', 'state' => 'TX'],
            ['stadium_name' => 'The Dome at America\'s Center', 'team_id' => $this->getTeamId('STL'), 'roof_type' => 'Indoor', 'longitude' => -90.188611, 'latitude' => 38.632778, 'stadium_azimuth_angle' => 15, 'city' => 'St. Louis', 'state' => 'MO'],
            ['stadium_name' => 'Three Rivers Stadium', 'team_id' => $this->getTeamId('PIT'), 'roof_type' => 'Outdoor', 'longitude' => -80.012778, 'latitude' => 40.446667, 'stadium_azimuth_angle' => 0, 'city' => 'Pittsburgh', 'state' => 'PA'],
            ['stadium_name' => 'TIAA Bank Field', 'team_id' => $this->getTeamId('JAX'), 'roof_type' => 'Outdoor', 'longitude' => -81.6375, 'latitude' => 30.323889, 'stadium_azimuth_angle' => 16.7, 'city' => 'Jacksonville', 'state' => 'FL'],
            ['stadium_name' => 'Tiger Stadium', 'team_id' => $this->getTeamId('NO'), 'roof_type' => 'Outdoor', 'longitude' => -91.185556, 'latitude' => 30.411944, 'stadium_azimuth_angle' => 336, 'city' => 'Baton Rouge', 'state' => 'LA'],
            ['stadium_name' => 'Tottenham Hotspur Stadium', 'team_id' => $this->getTeamId('Tottenham'), 'roof_type' => 'Outdoor', 'longitude' => -0.066389, 'latitude' => 51.604444, 'stadium_azimuth_angle' => 0, 'city' => 'London', 'state' => 'England'],
            ['stadium_name' => 'Twickenham Stadium', 'team_id' => $this->getTeamId(''), 'roof_type' => 'Outdoor', 'longitude' => -0.341667, 'latitude' => 51.456111, 'stadium_azimuth_angle' => 324.8, 'city' => 'London', 'state' => 'England'],
            ['stadium_name' => 'U.S. Bank Stadium', 'team_id' => $this->getTeamId('MIN'), 'roof_type' => 'Indoor', 'longitude' => -93.258056, 'latitude' => 44.973889, 'stadium_azimuth_angle' => 309.9, 'city' => 'Minneapolis', 'state' => 'MN'],
            ['stadium_name' => 'Veterans Stadium', 'team_id' => $this->getTeamId('PHI'), 'roof_type' => 'Outdoor', 'longitude' => -75.171111, 'latitude' => 39.906667, 'stadium_azimuth_angle' => 90, 'city' => 'Philadelphia', 'state' => 'PA'],
            ['stadium_name' => 'Wembley Stadium', 'team_id' => $this->getTeamId(''), 'roof_type' => 'Outdoor', 'longitude' => -0.279722, 'latitude' => 51.555833, 'stadium_azimuth_angle' => 90, 'city' => 'London', 'state' => 'England'],
        ];

        foreach ($stadiums as &$stadium) {
            $stadium['active'] = $this->isActive($stadium['stadium_name']);
            $stadium['created_at'] = $now;
            $stadium['updated_at'] = $now;
        }

        DB::table('nfl_stadiums')->insert($stadiums);
    }

    private function getTeamId($teamAbbreviation)
    {
        return DB::table('nfl_teams')->where('abbreviation', $teamAbbreviation)->value('id');
    }

    private function isActive($stadiumName): bool
    {
        return DB::table('nfl_teams')->where('stadium', $stadiumName)->exists();
    }
}
