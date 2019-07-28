<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ForgotPasswordTest extends TestCase
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
    public function the_forgot_password_email_is_sent_successfully()
    {
        $response = $this->post('api/auth/forgot_password', [
            'email' => 'john@example.com',
        ]);

        $response->assertJsonStructure([
            'status'
        ])->assertStatus(200);
    }

    /** @test */
    public function the_forgot_password_email_is_not_sent_if_user_does_not_exist()
    {
        $response = $this->post('api/auth/forgot_password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function validation_fails_if_email_not_provided()
    {
        $response = $this->json('POST', 'api/auth/forgot_password');

        $response->assertJsonValidationErrors([
            'email',
        ])->assertStatus(422);
    }

    /** @test */
    public function validation_fails_if_email_not_valid()
    {
        $response = $this->json('POST', 'api/auth/forgot_password', [
            'email' => 'not_a_valid_email',
        ]);

        $response->assertJsonValidationErrors([
            'email',
        ])->assertStatus(422);
    }
}
