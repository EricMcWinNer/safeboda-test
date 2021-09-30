<?php

namespace Controllers\PromoCodeController;

use App\Models\Event;
use App\Models\PromoCode;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ReadAllTest extends \TestCase
{
    use DatabaseMigrations;

    /**
     *
     * @test Read Promo Code Positive Test
     *
     */
    public function readPromoCodePositiveTest(): void
    {
        Event::factory()->has(PromoCode::factory()->count(4))->create();
        $response = $this->json(
            "GET",
            "/promo-code/",
        );
        $response->seeJson(
            [
                'status' => 'success'
            ]
        );
        $response->assertResponseStatus(200);
        $data = $response->response['data'];
        $this->assertCount(4, $data);
    }
}
