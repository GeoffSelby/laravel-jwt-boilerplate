<?php

namespace Tests\Feature;

use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
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

        DB::table('password_resets')->insert([
            'email' => 'john@example.com',
            'token' => bcrypt('my_super_secret_code'),
            'created_at' => Carbon::now()
        ]);
    }

    /** @test */
    public function a_user_can_reset_their_password()
    {
        $response = $this->post('api/auth/reset_password', [
            'email' => 'john@example.com',
            'token' => 'my_super_secret_code',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertJson([
            'status' => 'ok',
        ])->assertStatus(200);
    }

    /** @test */
    public function a_password_cannot_be_reset_without_a_valid_reset_token()
    {
        $response = $this->post('api/auth/reset_password', [
            'email' => 'john@example.com',
            'token' => 'invalid_token_code',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(500);
    }

    /** @test */
    public function email_is_required()
    {
        $response = $this->json('POST', 'api/auth/reset_password', [
            'token' => 'my_super_secret_code',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertJsonValidationErrors([
            'email',
        ])->assertStatus(422);
    }

    /** @test */
    public function email_must_be_valid()
    {
        $response = $this->json('POST', 'api/auth/reset_password', [
            'email' => 'invalid_email',
            'token' => 'my_super_secret_code',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertJsonValidationErrors([
            'email',
        ])->assertStatus(422);
    }

    /** @test */
    public function token_is_required()
    {
        $response = $this->json('POST', 'api/auth/reset_password', [
            'email' => 'john@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertJsonValidationErrors([
            'token',
        ])->assertStatus(422);
    }

    /** @test */
    public function password_is_required()
    {
        $response = $this->json('POST', 'api/auth/reset_password', [
            'email' => 'john@example.com',
            'token' => 'my_super_secret_token',
        ]);

        $response->assertJsonValidationErrors([
            'password',
        ])->assertStatus(422);
    }

    /** @test */
    public function password_must_be_confirmed()
    {
        $response = $this->json('POST', 'api/auth/reset_password', [
            'email' => 'john@example.com',
            'token' => 'my_super_secret_token',
            'password' => 'newpassword',
            'password_confirmation' => 'nepassword',
        ]);

        $response->assertJsonValidationErrors([
            'password',
        ])->assertStatus(422);
    }
}
