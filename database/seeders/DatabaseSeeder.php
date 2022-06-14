<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Venue;
use App\Models\Session;
use App\Models\Activity;
use App\Models\ActivityType;
use Illuminate\Database\Seeder;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([ActivityTypeSeeder::class]);

        Venue::factory()
        ->count(10)
        ->create();


        /* For each user created also create activities
        For each activity also create sessions */ 
        User::factory()->count(3)
            ->has(Activity::factory()->count(3)
                ->has(Session::factory()->count(10))
            )
        ->create();











    }
}
