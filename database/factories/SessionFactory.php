<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $activity = Activity::inRandomOrder()->first();

        return [

            'activity_id' => $activity->id,
            'parent_id' => null,
            'session_day' =>rand(1,7),
            'start_date' => $this->faker->date(),
            'finish_date' => $this->faker->date(),
            'start_time' => $this->faker->time(),
            'finish_time' =>$this->faker->time(),
            'ends_on' => 1,
            'recurrance_type' => 1,
            'recurrance_interval' => 1,
            'recurrance_monthly_interval' => null,
            'hours' => null,
            'updated_by' => $this->faker->numerify('###'),
        ];
    }
}
