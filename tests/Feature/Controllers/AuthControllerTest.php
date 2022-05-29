<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    const NAME = 'Random User';
    const EMAIL = 'randomemail@gmail.com';
    const PASSWORD = 'randompassword';
    /**
     * Test api login failed.
     * 
     * @return void
     */
    public function testLoginFailed()
    {
        $response = $this->json('POST', route('api.login'), [
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test api register failed.
     * 
     * @return void
     */
    public function testRegisterFailed()
    {
        $response = $this->json('POST', route('api.register'), [
            'name' => self::NAME,
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test api register success.
     * 
     * @return void
     */
    public function testRegisterSuccess()
    {
        $response = $this->json('POST', route('api.register'), [
            'name' => self::NAME,
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
        ]);

        $response->assertStatus(201);
    }

    /**
     * Test api login success.
     * 
     * @return void
     */
    public function testLoginSuccess()
    {
        $user = User::factory()->create([
            'name' => self::NAME,
            'email' => self::EMAIL,
            'password' => Hash::make(self::PASSWORD),
        ]);

        $response = $this->json('POST', route('api.login'), [
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
        ]);
    }

    /**
     * Test get api user authenticated.
     * 
     * @return void
     */
    public function testGetUserAuthenticated()
    {
        $user = User::factory()->create([
            'name' => self::NAME,
            'email' => self::EMAIL,
            'password' => Hash::make(self::PASSWORD),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;
        
        $response = $this->json('GET', route('api.user'), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
        ]);
        $response->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
