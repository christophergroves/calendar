<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Venue;
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

        User::factory()
        ->count(10)
        ->create();

        Venue::factory()
        ->count(50)
        ->create();

    }
}
