<?php

namespace Controllers\PromoCodeController;

use App\Models\Event;
use App\Models\PromoCode;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ReadAllActiveTest extends \TestCase
{
    use DatabaseMigrations;

    /**
     * @test Test Read active promo code positive test
     */
    public function getActivePromoCodesPositiveTest(): void
    {
        Event::factory()->has(PromoCode::factory()->count(3)->active())->create();
        Event::factory()->has(PromoCode::factory()->count(3)->inActive())->create();
        $response = $this->json(
            "GET",
            "/promo-code/active",
        );
        $response->assertResponseStatus(200);
        $response->seeJson(
            [
                'status' => 'success'
            ]
        );
        $this->assertCount(3, $response->response['data']);
    }


}
