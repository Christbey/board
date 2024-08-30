<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollegeFootballTeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'school' => $this->school,
            'mascot' => $this->mascot,
            'abbreviation' => $this->abbreviation,
            'color' => $this->color,
            'logos' => json_decode($this->logos),
            'twitter' => $this->twitter,
            'venue' => [
                'id' => $this->venue_id,
                'name' => $this->venue_name,
                'city' => $this->city,
                'state' => $this->state,
                'zip' => $this->zip,
                'country_code' => $this->country_code,
                'timezone' => $this->timezone,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'elevation' => $this->elevation,
                'capacity' => $this->capacity,
                'year_constructed' => $this->year_constructed,
                'grass' => $this->grass,
                'dome' => $this->dome,
            ],
            'conference_id' => $this->conference_id,
            // Add any other fields you need to expose

        ];


    }
}
