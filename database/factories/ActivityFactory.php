<?php

namespace Database\Factories;

use App\Models\Venue;
use App\Models\ActivityType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $activity_type = ActivityType::inRandomOrder()->first();
        $venue = Venue::inRandomOrder()->first();

        return [
            'description' => $this->faker->text(10),
            'updated_by' => $this->faker->numerify('###'),
            'activity_type_id' => $activity_type->id,
            'venue_id' => $venue->id,
        ];
    }
}
