<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class RegisterWithoutEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_without_email_generates_placeholder_email()
    {
        Bus::fake();

        $phone = '9876543210';
        $otp = '123456';

        // put OTP in cache (same key as controller expects)
        Cache::put("otp:registration:mobile:{$phone}", $otp, now()->addMinutes(10));

        $payload = [
            'name' => 'Test User',
            'phone' => $phone,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'otp' => $otp,
            'dob' => '1990-01-01',
            'gender' => 'male'
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'phone' => $phone,
            'email' => $phone . '@entekeralam.kerala.gov.in'
        ]);

        $user = User::where('phone', $phone)->first();
        $this->assertNotNull($user);
        $this->assertEquals($phone . '@entekeralam.kerala.gov.in', $user->email);
    }
}
