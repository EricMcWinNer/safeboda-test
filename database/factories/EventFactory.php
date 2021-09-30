<?php


namespace Database\Factories;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;


class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition() {
        return [
            'name' => $this->faker->bs(),
            'description' => $this->faker->sentence(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'location' => $this->faker->address(),
            'event_starts_at' => Carbon::yesterday(),
            'event_ends_at' => Carbon::tomorrow()->addDay(),
        ];
    }
}
