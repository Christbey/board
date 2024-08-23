<?php

namespace Database\Factories;

use App\Models\NflEspnAthlete;
use App\Models\NflEspnTeam;
use Illuminate\Database\Eloquent\Factories\Factory;

class NflEspnAthleteFactory extends Factory
{
    protected $model = NflEspnAthlete::class;

    public function definition()
    {
        return [
            'team_id' => NflEspnTeam::factory(), // Ensure this is correctly linked to a team
            'athlete_id' => $this->faker->unique()->randomNumber(),
            'jersey' => $this->faker->numberBetween(1, 99),
            'season_year' => $this->faker->year(),
            'uid' => $this->generateUid(), // Generate uid in the correct format
            'guid' => $this->faker->uuid,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'full_name' => $this->faker->name,
            'display_name' => $this->faker->name,
            'short_name' => $this->faker->word,
            'weight' => $this->faker->numberBetween(180, 250),
            'display_weight' => "{$this->faker->numberBetween(180, 250)} lbs",
            'height' => $this->faker->numberBetween(65, 80), // Height in inches
            'display_height' => "{$this->faker->numberBetween(5, 6)}'{$this->faker->numberBetween(0, 11)}\"",
            'age' => $this->faker->numberBetween(20, 35),
            'date_of_birth' => $this->faker->date(),
            'debut_year' => $this->faker->year(),
            'position' => $this->faker->randomElement(['QB', 'RB', 'WR', 'TE', 'K']),
            'status' => $this->faker->randomElement(['Active', 'Injured', 'Retired']),
        ];
    }

    /**
     * Generate a UID in the format 's:20~l:28~t:34'
     *
     * @return string
     */
    protected function generateUid()
    {
        return 's:' . $this->faker->numberBetween(10, 99) .
            '~l:' . $this->faker->numberBetween(10, 99) .
            '~t:' . $this->faker->numberBetween(10, 99);
    }
}
