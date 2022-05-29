<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 5 offers with users
        $offers = Offer::factory()->count(5)->create();
        $offers->each(function ($offer) {
            $offer->users()->saveMany(User::factory()->count(3)->create());
        });
    }
}
