<?php

namespace Controllers\PromoCodeController;

use App\Models\Event;
use Carbon\Carbon;
use Faker\Factory;
use Laravel\Lumen\Testing\DatabaseMigrations;

class CreateTest extends \TestCase
{
    use DatabaseMigrations;

    /**
     * @return array[][]
     */
    public function createPromoCodePositiveTestsProvider(): array
    {
        $faker = Factory::create();
        return [
            'Create promo code' => [
                [
                    'code' => strtoupper($faker->word),
                    'event_id' => 1,
                    'amount' => $faker->randomFloat(2),
                    'valid_radius' => $faker->randomFloat(2),
                    'expires_at' => Carbon::parse($faker->dateTime())->toDateTimeString(),
                ]
            ],
        ];
    }

    /**
     * @test Test create promo code positive test
     *
     * @dataProvider createPromoCodePositiveTestsProvider
     *
     * @param array $request
     *
     */
    public function createPromoCodePositiveTest(array $request): void
    {
        Event::factory()->create();
        $response = $this->json(
            "POST",
            "/promo-code/",
            $request
        );
        $response->seeJson(
            [
                'status' => 'success'
            ]
        );
        $response->assertResponseStatus(201);
        $response->seeJson(
            [
                'status' => 'success'
            ]
        );
    }
}
