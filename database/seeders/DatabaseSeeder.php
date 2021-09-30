<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        Event::factory()->has(PromoCode::factory()->count(3)->active())->create();
    }
}
