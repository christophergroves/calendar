<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activity_types = [
            ['name' => 'Training (Accredited)','category' => 'Training', 'list_position' => 1],
            ['name' => 'Training (Non-Accredited)','category' => 'Training', 'list_position' => 2],
            ['name' => 'Work Prep','category' => 'WorkExp', 'list_position' => 3],
            ['name' => 'Work Placement','category' => 'WorkExp', 'list_position' => 4],
            ['name' => 'Work Paid','category' => 'WorkExp', 'list_position' => 5],
            ['name' => 'Planning & Support','category' => 'ProgPlanning', 'list_position' => 6],
        ];

        foreach($activity_types as $type)
        {
            DB::table('activity_types')->insert([
                'name' => $type['name'],
                'category' => $type['category'],
                'list_position' => $type['list_position'],
                'updated_by' => 1,
            ]);
        }

    }



    
    
}
