<?php

namespace Controllers\EventController;

use App\Models\Event;
use App\Models\PromoCode;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ReadTest extends \TestCase
{
    use DatabaseMigrations;

    /**
     * @test Read single event positive test
     */
    public function readEventPositiveTest(): void
    {
        $event = Event::factory()->create();
        $id = $event->id;
        $response = $this->json(
            "GET",
            "/event/$id",
        );
        $response->assertResponseStatus(200);
        $response->seeJson(
            [
                'status' => 'success',
            ]
        );
    }

    /**
     * @test Test read single event with all promo codes
     */
    public function readEventWithPromoCodesPositiveTest(): void
    {
        $event = Event::factory()
            ->has(PromoCode::factory()->count(3))->create();
        $id = $event->id;
        $response = $this->json(
            "GET",
            "/event/$id/codes",
        );
        $response->assertResponseStatus(200);
        $response->seeJson(
            [
                'status' => 'success',
            ]
        );
    }

}
