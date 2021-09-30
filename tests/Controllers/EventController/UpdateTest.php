<?php

namespace Controllers\EventController;
use App\Models\Event;
use App\Models\PromoCode;
use Carbon\Carbon;
use Faker\Factory;
use Laravel\Lumen\Testing\DatabaseMigrations;
class UpdateTest extends \TestCase
{
    use DatabaseMigrations;

    public function updateRequestPositiveProvider(): array
    {
        $faker = Factory::create();
        $name = $faker->bs();
        $description = $faker->sentence();
        $latitude = 4.837907;
        $longitude = 7.010747;
        $location = "Opposite BMX Tower, Along Airport Road, Rukpokwu 500102, Port Harcourt, Rivers";
        $event_starts_at = Carbon::now()->toDateTimeString();
        $event_ends_at = Carbon::now()->addDay()->toDateTimeString();
        return [
            'Test update event name' => [
                [
                    'name' => $name
                ]
            ],
            'Test updates all values except location' => [
                [
                    'name' => $name,
                    'description' => $description,
                    'event_starts_at' => $event_starts_at,
                    'event_ends_at' => $event_ends_at,
                ]
            ],
            'Test with latitude and longitude' => [
                [
                    'name' => $name,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'event_starts_at' => $event_starts_at,
                    'event_ends_at' => $event_ends_at,
                ]
            ],
            'Test updates everything except name' => [
                [
                    'description' => $description,
                    'location' => $location,
                    'event_starts_at' => $event_starts_at,
                    'event_ends_at' => $event_ends_at,
                ]
            ],
            'Test updates everything' => [
                [
                    'name' => $name,
                    'description' => $description,
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'event_starts_at' => $event_starts_at,
                    'event_ends_at' => $event_ends_at,
                ]
            ],
        ];
    }


    /**
     * @test Update Event Positive Tests
     * @dataProvider updateRequestPositiveProvider
     *
     * @param $updateRequest
     */
    public function updateEventPositiveTest($updateRequest): void
    {
        $event = Event::factory()
            ->has(PromoCode::factory()->count(3))->create();
        $id = $event->id;
        $response = $this->json(
            'POST',
            "/event/$id/update",
            $updateRequest
        );
        $response->seeJson(
            [
                "status" => "success"
            ]
        );
        $response->assertResponseStatus(201);
    }
}
