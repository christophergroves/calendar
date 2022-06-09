<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        // $response = $this->get('/');

        // $response->assertStatus(200);




    $payload = ["requestId"=> 85261523,"customerId" => 256945,"vehicle"=>["id"=>9763928242,"value"=>11294,"last_listed"=>null]];

    $response = $this->json('PUT', 'api/store', $payload);
           

    $response->assertStatus(200);



















    }
}
