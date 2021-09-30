<?php

namespace Controllers\PromoCodeController;

use App\Models\Event;
use App\Models\PromoCode;
use Faker\Factory;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UseTest extends \TestCase
{
    use DatabaseMigrations;

    public function validateRideWithPromoCodeTestProvider(): array
    {
        $faker = Factory::create();
        return [
            'Test with origin lat/long and destination lat/long' => [
                [
                    'code' => 'TESTCODE',
                    'event_valid_radius' => 2500,
                    'event_latitude' => 4.837907,
                    'event_longitude' => 7.010747
                ],
                [
                    'code' => 'TESTCODE',
                    'origin_latitude' => 4.829045,
                    'origin_longitude' => 7.003176,
                    'destination_latitude' => 4.838041,
                    'destination_longitude' => 7.025647
                ]
            ]
        ];
    }


    /**
     * @test Test validate ride with promo code
     *
     * @dataProvider validateRideWithPromoCodeTestProvider
     */
    public function validateRideWithPromoCodePositiveTest(array $eventDetails, array $request)
    {
        $event = Event::factory()->has
        (
            PromoCode::factory()
                ->active()->state(
                    function (array $attributes) use ($eventDetails) {
                        return [
                            'code' => $eventDetails['code'],
                            'valid_radius' => $eventDetails['event_valid_radius']
                        ];
                    }
                )
        )->create(
            [
                'latitude' => $eventDetails['event_latitude'],
                'longitude' => $eventDetails['event_longitude'],
            ]
        );
        $event->load('promoCodes');
        $promoCodes = $event->promoCodes;
        $id = $promoCodes->first()->id;

        $response = $this->json(
            "POST",
            "/promo-code/use",
            $request
        );
        $response->seeJson(
            [
                'message' => 'Promo code is valid',
                'status' => 'success',
            ]
        );
        $response->assertResponseStatus(201);
    }
}
