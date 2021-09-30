<?php

namespace Controllers\EventController;

use Carbon\Carbon;
use Faker\Factory;
use Laravel\Lumen\Testing\DatabaseMigrations;

class CreateTest extends \TestCase
{
    use DatabaseMigrations;

    /**
     * @return array[][]
     */
    public function createEventPositiveTestsProvider(): array
    {
        $faker = Factory::create();
        $latitude = 4.869848;
        $longitude = 6.993064;
        $location = "52 Tombia St, Rivers 500272, Port Harcourt";
        $event_starts_at = Carbon::now()->toDateTimeString();
        $event_ends_at = Carbon::now()->addDay()->toDateTimeString();
        return [
            'Test valid request with longitude and latitude' => [
                [
                    'name' => $faker->bs(),
                    'description' => $faker->sentence(),
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'event_starts_at' => $event_starts_at,
                    'event_ends_at' => $event_ends_at,
                ]
            ],
            'Test valid request with location' => [
                [
                    'name' => $faker->bs(),
                    'description' => $faker->sentence(),
                    'location' => $location,
                    'event_starts_at' => $event_starts_at,
                    'event_ends_at' => $event_ends_at,
                ]
            ]
        ];
    }

    public function createEventNegativeTestsProvider(): array
    {
        $faker = Factory::create();
        $name = $faker->bs();
        $description = $faker->sentence();
        $latitude = $faker->latitude();
        $longitude = $faker->longitude();
        $location = $faker->address();
        $event_starts_at = Carbon::now()->toDateTimeString();
        $event_ends_at = Carbon::now()->addDay()->toDateTimeString();
        return [
            'Test empty request' => [
                []
            ],
            'Test with missing longitude' => [
                [
                    'name' => $name,
                    'description' => $description,
                    'latitude' => $latitude,
                    'event_starts_at' => $event_starts_at,
                    'event_ends_at' => $event_ends_at,
                ]
            ],
            'Test with missing latitude' => [
                [
                    'name' => $name,
                    'description' => $description,
                    'longitude' => $longitude,
                    'event_starts_at' => $event_starts_at,
                    'event_ends_at' => $event_ends_at,
                ]
            ],
            'Test with missing location' => [
                [
                    'name' => $name,
                    'description' => $description,
                    'event_starts_at' => $event_starts_at,
                    'event_ends_at' => $event_ends_at,
                ]
            ],
            'Test with missing description' => [
                [
                    'name' => $name,
                    'location' => $location,
                    'event_starts_at' => $event_starts_at,
                    'event_ends_at' => $event_ends_at,
                ]
            ],
            'Test with missing name' => [
                [
                    'description' => $description,
                    'location' => $location,
                    'event_starts_at' => $event_starts_at,
                    'event_ends_at' => $event_ends_at,
                ]
            ],
            'Test with location, latitude and longitude' => [
                [
                    'name' => $name,
                    'description' => $description,
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'location' => $location,
                    'event_starts_at' => $event_starts_at,
                    'event_ends_at' => $event_ends_at,
                ]
            ],
        ];
    }

    /**
     * @test Test valid event creation.
     * @dataProvider createEventPositiveTestsProvider
     * @param array $createRequest
     */
    public function createEventPositiveTests(array $createRequest): void
    {
        $response = $this->json(
            'POST',
            '/event',
            $createRequest
        );
        $response->seeJson(
            [
                'message' => 'Event created successfully',
                'status' => 'success'
            ]
        );
        $response->assertResponseStatus(201);
        $event = $response->response['data'];
        $this->seeInDatabase(
            'events',
            [
                'id' => $event['id']
            ]
        );
    }

    /**
     * @test Test user errors for create event request will generate 400 error code
     * @dataProvider createEventNegativeTestsProvider
     * @param array $createRequest
     */
    public function createEventNegativeTests(array $createRequest): void
    {
        $this->json(
            'POST',
            '/event',
            $createRequest
        )
            ->assertResponseStatus(400);
    }
}
