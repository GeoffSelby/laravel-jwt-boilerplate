<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $user = new User([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password'
        ]);

        $user->save();
    }

    /** @test */
    public function a_user_can_register()
    {
        $response = $this->post('api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ])->assertStatus(200);
    }

    /** @test */
    public function a_user_cannot_register_without_a_name()
    {
        $response = $this->json('POST', 'api/auth/register', [
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertJsonValidationErrors([
            'name'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_user_cannot_register_without_an_email()
    {
        $response = $this->json('POST', 'api/auth/register', [
            'name' => 'Jane Doe',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertJsonValidationErrors([
            'email'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_user_cannot_register_if_email_is_not_valid()
    {
        $response = $this->json('POST', 'api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'not_an_email',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertJsonValidationErrors([
            'email'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_user_cannot_register_if_email_is_taken()
    {
        $response = $this->json('POST', 'api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertJsonValidationErrors([
            'email'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_user_cannot_register_if_without_a_password()
    {
        $response = $this->json('POST', 'api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $response->assertJsonValidationErrors([
            'password'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_password_must_be_at_least_eight_characters()
    {
        $response = $this->json('POST', 'api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'passwrd',
        ]);

        $response->assertJsonValidationErrors([
            'password'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_user_must_confirm_password_to_register()
    {
        $response = $this->json('POST', 'api/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'passwrd',
        ]);

        $response->assertJsonValidationErrors([
            'password'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_registered_user_can_login()
    {
        $response = $this->post('api/auth/login', [
            'email'    => 'john@example.com',
            'password' => 'password'
        ]);

        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ])->assertStatus(200);
    }

    /** @test */
    public function an_email_is_required_to_login()
    {
        $response = $this->json('POST', 'api/auth/login', [
            'password' => 'password',
        ]);

        $response->assertJsonValidationErrors([
            'email'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_valid_email_is_required_to_login()
    {
        $response = $this->json('POST', 'api/auth/login', [
            'email' => 'not_an_email',
            'password' => 'password',
        ]);

        $response->assertJsonValidationErrors([
            'email'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_password_is_required_to_login()
    {
        $response = $this->json('POST', 'api/auth/login', [
            'email' => 'john@example',
        ]);

        $response->assertJsonValidationErrors([
            'password'
        ])->assertStatus(422);
    }

    /** @test */
    public function a_user_cannot_login_if_not_registered()
    {
        $response = $this->post('api/auth/login', [
            'email' => 'mary@example.com',
            'password' => 'password'
        ]);

        $response->assertJsonStructure([
            'error'
        ])->assertStatus(401);
    }

    /** @test */
    public function a_logged_in_user_can_logout()
    {
        $login = $this->post('api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $login->assertStatus(200);

        $response = $this->post('api/auth/logout');

        $response->assertJsonStructure([
            'message'
        ])->assertStatus(200);
    }

    /** @test */
    public function must_be_logged_in_to_logout()
    {
        $response = $this->post('api/auth/logout');

        $response->assertStatus(500);
    }

    /** @test */
    public function a_token_can_be_refreshed()
    {
        $response = $this->post('api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $responseJson = json_decode($response->getContent(), true);
        $token = $responseJson['access_token'];

        $refresh = $this->post('api/auth/refresh_token', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $refresh->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ])->assertStatus(200);
    }

    /** @test */
    public function an_invalid_token_cannot_be_refreshed()
    {
        $response = $this->post('api/auth/refresh_token', [], [
            'Authorization' => 'Bearer invalid_token',
        ]);

        $response->assertStatus(500);
    }

    /** @test */
    public function must_be_logged_in_to_refresh_token()
    {
        $response = $this->post('api/auth/refresh_token');

        $response->assertStatus(500);
    }
}
