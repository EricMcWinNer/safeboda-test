<?php


namespace Database\Factories;


use App\Models\PromoCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromoCodeFactory extends Factory
{
    protected $model = PromoCode::class;

    public function definition()
    {
        return [
            'code' => strtoupper($this->faker->word),
            'amount' => $this->faker->randomFloat(2),
            'valid_radius' => $this->faker->randomFloat(4, 1, 19),
            'expires_at' => $this->faker->dateTimeThisMonth
        ];
    }

    public function inActive(): PromoCodeFactory
    {
        return $this->state(
            function (array $attributes) {
                return [
                    'is_active' => false,
                    'expires_at' => Carbon::tomorrow()
                ];
            }
        );
    }

    public function inActiveAndExpired(): PromoCodeFactory
    {
        return $this->state(
            function (array $attributes) {
                return [
                    'is_active' => false,
                    'expires_at' => Carbon::yesterday()
                ];
            }
        );
    }

    public function activeButExpired(): PromoCodeFactory
    {
        return $this->state(
            function (array $attributes) {
                return [
                    'is_active' => true,
                    'expires_at' => Carbon::yesterday()
                ];
            }
        );
    }

    public function expired(): PromoCodeFactory
    {
        return $this->state(
            function (array $attributes) {
                return [
                    'expires_at' => Carbon::yesterday()
                ];
            }
        );
    }

    public function active(): PromoCodeFactory
    {
        return $this->state(
            function (array $attributes) {
                return [
                    'is_active' => true,
                    'expires_at' => Carbon::tomorrow()
                ];
            }
        );
    }
}
