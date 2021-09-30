<?php

namespace Controllers\PromoCodeController;

use App\Models\Event;
use App\Models\PromoCode;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ActivateTest extends \TestCase
{
    use DatabaseMigrations;

    /**
     *
     * @test Activate De-Activated Promo Code Positive Test
     *
     */
    public function activateDeactivatedPromoCode(): void
    {
        $event = Event::factory()->has(PromoCode::factory()->inActive())->create();
        $event->load('promoCodes');
        $promoCode = $event->promoCodes->first();
        $id = $promoCode->id;
        $response = $this->json(
            'GET',
            "promo-code/$id/activate"
        );
        $response->assertResponseStatus(200);
        $response->seeJson(
            [
                'message' => 'Promo code activated successfully',
                'status' => 'success'
            ]
        );
        $this->seeInDatabase(
            'promo_codes',
            [
                'is_active' => 1
            ]
        );
        $activePromoCodes = PromoCode::where('is_active', true)->count();
        $this->assertEquals(1, $activePromoCodes);
    }

}
