<?php

namespace Controllers\EventController;

use App\Models\Event;
use App\Models\PromoCode;
use Laravel\Lumen\Testing\DatabaseMigrations;


class ReadAllTest extends \TestCase
{
    use DatabaseMigrations;

    /**
     * @test Read all events positive test
     */
    public function readAllEventsPositiveTest() {
        $events = Event::factory()
            ->has(PromoCode::factory()->count(3))->count(5)->create();
        $response = $this->json(
            "GET",
            "/event",
        );
        $response->assertResponseStatus(200);
        $response->seeJson(
            [
                'status' => 'success',
            ]
        );
        $data = $response->response['data'];
        $this->assertCount(5, $data);
    }
}
