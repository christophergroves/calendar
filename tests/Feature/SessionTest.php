<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Session;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class SessionTest extends TestCase
{

    /**
     * Store a new one off session
     * @param Array $payload
     * @return void
     */
    public function test_new_one_off_store()
    {
        // clear the session table first
        Session::truncate();

        $payload = [
            "userId" =>	1,
            "action"	=> "edit-new",
            "sessionDate" => "2022-6-6",
            "activityId" =>	null,
            "sessionId" => null,
            "_token" =>	null,
            "activity_id" => 6,
            "session_date" => "06/06/2022",
            "start_time" =>	"10:15",
            "finish_time" => "12:15",
            "hours" =>	2,
            "recurrance_type" => 0,
            "recurrance_interval" => 1,
            "recurrance_monthly_interval" => null,
            "recurrance_day_single" =>	1,
            "start_date" =>	"06/06/2022",
            "ends_on" => 0,
            "recurrance_number" => null,
            "finish_date" => null
        ];

        // store this payload successfully?
        $response = $this->json('PUT', '/api/session/edit/store', $payload);
        $response->assertStatus(200);

    }


    /**
     * Store a new weekly session with 3 occurances
     * @param Array $payload
     * @return void
     */
    public function test_new_weekly_3_occurances_store()
    {
        $payload = [
            "userId" =>	1,
            "action"	=> "edit-new",
            "sessionDate" => "2022-6-7",
            "activityId" =>	null,
            "sessionId" => null,
            "_token" =>	null,
            "activity_id" => 6,
            "session_date" => "07/06/2022",
            "start_time" =>	"10:15",
            "finish_time" => "12:15",
            "hours" =>	2,
            "recurrance_type" => 1,
            "recurrance_interval" => 1,
            "recurrance_monthly_interval" => null,
            "recurrance_day_single" =>	1,
            "start_date" =>	"07/06/2022",
            "ends_on" => "ends_on_occurances",
            "recurrance_number" => 3,
            "finish_date" => null
        ];

        $response = $this->json('PUT', '/api/session/edit/store', $payload);
        $response->assertStatus(200);
        
    }

    /**
     * Store a new weekly session with no end date
     * @param Array $payload
     * @return void
     */
    public function test_new_weekly_continuous_store()
    {
        $payload = [
            "userId" =>	1,
            "action"	=> "edit-new",
            "sessionDate" => "2022-6-8",
            "activityId" =>	null,
            "sessionId" => null,
            "_token" =>	null,
            "activity_id" => 6,
            "session_date" => "08/06/2022",
            "start_time" =>	"10:15",
            "finish_time" => "12:15",
            "hours" =>	2,
            "recurrance_type" => 1,
            "recurrance_interval" => 1,
            "recurrance_monthly_interval" => null,
            "recurrance_day_single" =>	1,
            "start_date" =>	"08/06/2022",
            "ends_on" => "ends_never",
            "recurrance_number" => null,
            "finish_date" => null
        ];

        $response = $this->json('PUT', '/api/session/edit/store', $payload);
        $response->assertStatus(200);

    }



    /**
     * 3 new sessions stored?
     * @param Array $payload
     * @return void
     */
    public function test_sessions_count_stored()
    {
        $this->assertDatabaseCount('sessions', 3);
    }





}
