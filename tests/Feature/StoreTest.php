<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_store_payload()
    {
        // $response = $this->get('/');



        $payload = ["requestId"=> 85261523,"customerId" => 256945,"vehicle"=>["id"=>9763928242,"value"=>11294,"last_listed"=>null]];


//         {
//   "requestId": 85261523,
//   "customerId": 256945,
//   "vehicle": {
//     "id": 9763928242,
//     "value": 11294,
//     "last_listed": null
//   }
// }



      $response = $this->json('PUT', 'api/store', $payload);
           

        $response->assertStatus(200);
    }
}
