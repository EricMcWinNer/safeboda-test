<?php

namespace Controllers\PromoCodeController;

use App\Models\Event;
use App\Models\PromoCode;
use Laravel\Lumen\Testing\DatabaseMigrations;

class DeactivateTest extends \TestCase
{
    use DatabaseMigrations;

    /**
     * @test Test deactivate promo code
     */
    public function testDeactivatePromoCode(): void
    {
        $event = Event::factory()->has(PromoCode::factory()->count(3)->active())->create();
        $event->load('promoCodes');
        $firstPromoCode = $event->promoCodes->first();
        $id = $firstPromoCode->id;
        $response = $this->json(
            "GET",
            "/promo-code/$id/deactivate"
        );
        $response->assertResponseStatus(200);
        $response->seeJson(
            [
                'status' => 'success',
                'message' => 'Promo code was successfully deactivated'
            ]
        );
        $this->seeInDatabase(
            'promo_codes',
            [
                'is_active' => 0
            ]
        );
        $inactivePromoCodes = PromoCode::where('is_active', false)->count();
        $this->assertEquals(1, $inactivePromoCodes);
    }

}
