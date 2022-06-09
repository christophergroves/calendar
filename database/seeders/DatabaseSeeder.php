<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Vehicle;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();


        // Populate the Users and Vehicles Tables.
        $json_file = [];
        // Load files into array
        for($i=1;$i<=6;$i++)
        {
            $json_files[$i] = json_decode(file_get_contents(public_path() . "/api/request-00".$i.".json"), true);
        }


        
        foreach($json_files as $json_file)
        {
            // If user does not already exist create user
            $user = User::where('customer_id','=',$json_file['customerId'])->first();
            if(!$user)
            {
                $user_id = User::insertGetId([ 'customer_id' => $json_file['customerId'] ]);
            }
            else
            {
                $user_id = $user->id;
            }

            // create the user's vehicles
            // Vehicle::insert([ 
            //     'user_id' => $user_id, 
            //     'value' => $json_file['vehicle']['value'],
            //     'vehicle_id' => $json_file['vehicle']['id'],
            //     'last_listed' => $json_file['vehicle']['last_listed'] 
            // ]);
        }



    }
}
