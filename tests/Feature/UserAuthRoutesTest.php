<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAuthRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_redirect_from_root_to_schedule(): void
    {
        $response = $this->get('/');

        $response->assertStatus(301);
        $response->assertRedirect('/schedule');
    }

    public function test_redirect_to_login_if_not_logged_in(): void
    {
        $response = $this->get('/schedule');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_registration_page_is_available_if_user_not_logged_in(): void
    {
        $response = $this->get('/registration');

        $response->assertStatus(200);
        $response->assertViewIs('registration.index');
    }

    public function test_registration_page_is_not_available_if_user_logged(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/registration');

        $response->assertStatus(302);
        $response->assertRedirect('/home');

        $finalResponse = $this->actingAs($user)->get('/home');

        $finalResponse->assertStatus(301);
        $finalResponse->assertRedirect('/schedule');
    }

    public function test_redirect_to_email_verify_page_if_user_email_is_not_verified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'is_active' => true,
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/schedule');
        $response->assertStatus(302);
        $response->assertRedirect('verify-email');
    }

    public function test_access_denied_if_user_not_active(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => false,
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/schedule');
        $response->assertStatus(403);
    }

    public function test_redirect_from_login_to_schedule_if_logged_in(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'is_active' => true,
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/login');

        $response->assertStatus(302);
        $response->assertRedirect('/home');

        $finalResponse = $this->actingAs($user)->get('/home');

        $finalResponse->assertStatus(301);
        $finalResponse->assertRedirect('/schedule');
    }

    public function test_schedule_page_available_to_authenticated_user()
    {
        $user = User::factory()->create([
            'is_active' => true,
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/schedule');

        $response->assertStatus(200);
        $response->assertViewIs('schedule.index');
    }
}
