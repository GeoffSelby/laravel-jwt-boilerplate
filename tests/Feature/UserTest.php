<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $user = new User([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $user->save();
    }

    /** @test */
    public function the_authenticated_user_can_be_retrieved()
    {
        $response = $this->post('api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $responseJson = json_decode($response->getContent(), true);
        $token = $responseJson['access_token'];

        $this->get('api/auth/user?token=' . $token, [], [])->assertJson([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ])->assertStatus(200);
    }
}
