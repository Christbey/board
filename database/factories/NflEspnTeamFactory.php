<?php

namespace Database\Factories;

use App\Models\NflEspnTeam;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NflEspnTeamFactory extends Factory
{
    protected $model = NflEspnTeam::class;

    public function definition(): array
    {
        return [
            'team_id' => $this->faker->numberBetween(40, 60),
            'uid' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'abbreviation' => $this->faker->word(),
            'display_name' => $this->faker->name(),
            'short_display_name' => $this->faker->name(),
            'name' => $this->faker->name(),
            'nickname' => $this->faker->word(),
            'location' => $this->faker->word(),
            'color' => $this->faker->word(),
            'alternate_color' => $this->faker->word(),
            'is_active' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
