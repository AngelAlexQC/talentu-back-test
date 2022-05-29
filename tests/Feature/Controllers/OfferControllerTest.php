<?php

namespace Tests\Feature\Controllers;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OfferControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    const NAME = 'Random Offer';
    const STATUS = 'active';

    public function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    /**
     * Login before each test.
     * 
     * @return void
     */
    public function login()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
        ]);

        $this->actingAs($user);
    }

    /**
     * Test GET /api/offers
     * 
     * @return void
     */
    public function testGetOffers()
    {
        $this->seed();
        $response = $this->json('GET', route('api.offers.index'));
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'status',
                    'users' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'dni',
                            'dni_type',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test GET /api/offers/{id}
     * 
     * @return void
     */
    public function testGetOffer()
    {
        $offer = Offer::factory()->create();
        $users = User::factory()->count(2)->create();
        $offer->users()->sync($users->pluck('id'));
        $response = $this->json('GET', route('api.offers.show', $offer->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'status',
            'users' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'dni',
                    'dni_type',
                ],
            ],
        ]);
    }

    /**
     * Test POST /api/offers
     * 
     * @return void
     */
    public function testPostOffer()
    {
        $users = User::factory()->count(3)->create();
        $response = $this->json('POST', route('api.offers.store'), [
            'name' => self::NAME,
            'status' => self::STATUS,
            'users' => $users->pluck('id')->toArray(),
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
            'status',
            'users' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'dni',
                    'dni_type',
                ],
            ],
        ]);
        $response->assertJsonFragment([
            'name' => self::NAME,
            'status' => self::STATUS,
        ]);
    }

    /**
     * Test failed POST /api/offers
     * 
     * @return void
     */
    public function testFailedPostOffer()
    {
        $response = $this->json('POST', route('api.offers.store'), [
            'name' => self::NAME,
            'status' => self::STATUS,
        ]);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'users'
            ],
        ]);
    }

    /**
     * Test PUT /api/offers/{id}
     * 
     * @return void
     */
    public function testPutOffer()
    {
        $offer = Offer::factory()->create();
        $users = User::factory()->count(3)->create();
        $offer->users()->sync($users->pluck('id'));
        $response = $this->json('PUT', route('api.offers.update', $offer->id), [
            'name' => self::NAME,
            'status' => self::STATUS,
            'users' => $users->pluck('id')->toArray(),
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'status',
            'users' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'dni',
                    'dni_type',
                ],
            ],
        ]);
        $response->assertJsonFragment([
            'name' => self::NAME,
            'status' => self::STATUS,
        ]);
    }

    /**
     * Test failed PUT /api/offers/{id}
     * 
     * @return void
     */
    public function testFailedPutOffer()
    {
        $offer = Offer::factory()->create();
        $users = User::factory()->count(3)->create();
        $offer->users()->sync($users->pluck('id'));
        $response = $this->json('PUT', route('api.offers.update', $offer->id), [
            'name' => self::NAME,
            'status' => self::STATUS,
        ]);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'users'
            ],
        ]);
    }

    /**
     * Test DELETE /api/offers/{id}
     * 
     * @return void
     */
    public function testDeleteOffer()
    {
        $offer = Offer::factory()->create();
        $response = $this->json('DELETE', route('api.offers.destroy', $offer->id));
        $response->assertStatus(204);
    }
}
