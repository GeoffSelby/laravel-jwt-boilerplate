<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_email_can_be_verified()
    {
        $response = $this->post('api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertJson([
            'user_id' => 1,
        ]);

        $response->assertStatus(200);

        $responseJson = json_decode($response->getContent(), true);
        $user_id = $responseJson['user_id'];

        $verify = $this->post('api/email/verify/' . $user_id);

        $verify->assertJson([
            'verified' => true,
        ])->assertStatus(200);
    }

    /** @test */
    public function an_email_cannot_be_verified_without_an_id()
    {
        $response = $this->post('api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertJson([
            'user_id' => 1,
        ]);

        $response->assertStatus(200);

        $verify = $this->post('api/email/verify/42');

        $verify->assertStatus(500);
    }

    /** @test */
    public function a_user_must_be_logged_in_to_verify_email()
    {
        $user = new User([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $user->save();

        $verify = $this->post('api/email/verify/1');

        $verify->assertStatus(500);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    /** @test */
    public function an_email_cannot_be_verified_twice()
    {
        $response = $this->post('api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertJson([
            'user_id' => 1,
        ]);

        $response->assertStatus(200);

        $responseJson = json_decode($response->getContent(), true);
        $user_id = $responseJson['user_id'];

        $verify = $this->post('api/email/verify/' . $user_id);

        $verify->assertJson([
            'verified' => true,
        ])->assertStatus(200);

        $reverify = $this->post('api/email/verify/' . $user_id);

        $reverify->assertJson([
            'message' => 'Email already verified',
        ])->assertStatus(200);
    }

    /** @test */
    public function a_verification_email_can_be_resent()
    {
        $response = $this->post('api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertJson([
            'user_id' => 1,
        ]);

        $response->assertStatus(200);

        $resend = $this->post('api/email/resend');

        $resend->assertJson([
            'message' => 'Verification email resent',
        ])->assertStatus(200);
    }

    /** @test */
    public function a_verification_email_cannot_be_resent_if_email_is_verified()
    {
        $response = $this->post('api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertJson([
            'user_id' => 1,
        ]);

        $response->assertStatus(200);

        $responseJson = json_decode($response->getContent(), true);
        $user_id = $responseJson['user_id'];

        $verify = $this->post('api/email/verify/' . $user_id);

        $verify->assertJson([
            'verified' => true,
        ])->assertStatus(200);

        $resend = $this->post('api/email/resend');

        $resend->assertJson([
            'message' => 'Email already verified',
        ])->assertStatus(200);
    }
}
