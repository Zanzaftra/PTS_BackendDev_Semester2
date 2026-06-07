<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAuthFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_login_accepts_session_fallback_user(): void
    {
        session()->put('mock_customer_users.test@example.com', [
            'nama_pelanggan' => 'User Session',
            'email' => 'test@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $this->postJson('/customer/login', [
            'email' => 'test@example.com',
            'password' => 'secret123',
        ])->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_customer_dashboard_uses_real_logged_in_name_when_database_is_unavailable(): void
    {
        session([
            'customer_logged_in' => true,
            'customer_id' => 999,
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
        ]);

        $response = $this->get('/customer/dashboard');

        $response->assertStatus(200)
            ->assertSee('Budi Santoso');
    }

    public function test_customer_can_register_and_login_with_non_demo_credentials(): void
    {
        $email = 'baru@example.com';
        $password = 'password123';

        $registerResponse = $this->postJson('/customer/register', [
            'nama_pelanggan' => 'User Baru',
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $registerResponse->assertStatus(200)
            ->assertJsonPath('success', true);

        $loginResponse = $this->postJson('/customer/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
