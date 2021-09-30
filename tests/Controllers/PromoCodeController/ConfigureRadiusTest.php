<?php

namespace Controllers\PromoCodeController;

use App\Models\Event;
use App\Models\PromoCode;
use Faker\Factory;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ConfigureRadiusTest extends \TestCase
{
    use DatabaseMigrations;

    /**
     * @return array[][]
     */
    public function configurePromoCodePositiveTestProvider(): array
    {
        $faker = Factory::create();
        return [
            'Test whole number' => [
                [
                    'valid_radius' => $faker->randomDigit(),
                ]
            ],
            'Test floating point number' => [
                [
                    'valid_radius' => $faker->randomFloat(4),
                ]
            ]
        ];
    }

    /**
     * @test Positive test for configuration of promo code radius
     *
     * @dataProvider configurePromoCodePositiveTestProvider
     * @param array $configureRequest
     */
    public function configurePromoCodePositiveTest(array $configureRequest): void
    {
        $event = Event::factory()->has(PromoCode::factory()->active())->create();
        $event->load('promoCodes');
        $promoCode = $event->promoCodes->first();
        $id = $promoCode->id;
        $newRadius = $configureRequest['valid_radius'];
        $response = $this->json(
            "POST",
            "promo-code/$id/configure-radius",
            $configureRequest
        );
        $response->seeJson(
            [
                'message' => 'Promo Code radius updated successfully',
                'status' => 'success'
            ]
        );
        $response->assertResponseStatus(201);
        $this->seeInDatabase(
            'promo_codes',
            [
                'valid_radius' => $newRadius
            ]
        );
        $promoCode->refresh();
        $this->assertEquals($newRadius, $promoCode->valid_radius);
    }
}
