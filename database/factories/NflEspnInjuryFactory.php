<?php

namespace Database\Factories;

use App\Models\NflEspnAthlete;
use App\Models\NflEspnInjury;
use App\Models\NflEspnTeam;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NflEspnInjuryFactory extends Factory
{
    protected $model = NflEspnInjury::class;

    public function definition(): array
    {
        return [
            'injury_id' => $this->faker->word(),
            'type' => $this->faker->word(),
            'status' => $this->faker->word(),
            'date' => Carbon::now(),
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'team_id' => NflEspnTeam::factory(),
            'athlete_id' => NflEspnAthlete::factory(),
        ];
    }
}
