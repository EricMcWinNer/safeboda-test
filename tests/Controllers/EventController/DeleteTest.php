<?php

namespace Controllers\EventController;

use App\Models\Event;
use App\Models\PromoCode;
use Laravel\Lumen\Testing\DatabaseMigrations;

class DeleteTest extends \TestCase
{
    use DatabaseMigrations;
    /**
     * @test Delete event positive test
     */
    public function deleteEventPositiveTest(): void
    {
        $event = Event::factory()->has(PromoCode::factory()->count(4), 'promoCodes')->create();
        $id = $event->id;
        $promoCodes = $event->promoCodes;
        $response = $this->json(
            "GET",
            "event/$id/delete",
        );
        $response->assertResponseStatus(200);
        $response->seeJson(["status" => "success", "message" => "Event deleted successfully"]);
        $this->notSeeInDatabase(
            'events',
            [
                'id' => $event->id
            ]
        );
        $this->notSeeInDatabase(
            'promo_codes',
            [
                'id' => $promoCodes->first()->id
            ]
        );
    }
}
