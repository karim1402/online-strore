<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OtpAuthenticationTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();
        // Clean up test user if exists
        User::where('email', 'dev.karim12@gmail.com')->forceDelete();
        DB::table('password_reset_tokens')->where('email', 'dev.karim12@gmail.com')->delete();
    }

    protected function tearDown(): void
    {
        // Clean up test user
        User::where('email', 'dev.karim12@gmail.com')->forceDelete();
        DB::table('password_reset_tokens')->where('email', 'dev.karim12@gmail.com')->delete();
        parent::tearDown();
    }

    public function test_registration_generates_otp()
    {
        Mail::fake();

        $response = $this->postJson('/api/user/register', [
            'name' => 'Test OTP User',
            'email' => 'dev.karim12@gmail.com',
            'password' => 'password123',
            'phone' => '1234567890',
        ]);

        $response->assertStatus(201);
        
        $this->assertDatabaseHas('users', [
            'email' => 'dev.karim12@gmail.com',
        ]);

        $user = User::where('email', 'dev.karim12@gmail.com')->first();
        $this->assertNotNull($user->verification_code);
        $this->assertNotNull($user->verification_code_expires_at);
    }

    public function test_verify_email_success()
    {
        $user = User::create([
            'name' => 'Test OTP User',
            'email' => 'dev.karim12@gmail.com',
            'password' => Hash::make('password123'),
            'phone' => '1234567890',
            'verification_code' => '1234',
            'verification_code_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        $response = $this->postJson('/api/user/verify-email', [
            'email' => 'dev.karim12@gmail.com',
            'code' => '1234',
        ]);

        $response->assertStatus(200);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertNull($user->fresh()->verification_code);
    }

    public function test_verify_email_invalid_code()
    {
        $user = User::create([
            'name' => 'Test OTP User',
            'email' => 'dev.karim12@gmail.com',
            'password' => Hash::make('password123'),
            'phone' => '1234567890',
            'verification_code' => '1234',
            'verification_code_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        $response = $this->postJson('/api/user/verify-email', [
            'email' => 'dev.karim12@gmail.com',
            'code' => '9999',
        ]);

        $response->assertStatus(400); // Assuming 400 for invalid code based on my implementation
    }

    public function test_resend_verification_code()
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Test OTP User',
            'email' => 'dev.karim12@gmail.com',
            'password' => Hash::make('password123'),
            'phone' => '1234567890',
            'verification_code' => '1111',
            'verification_code_expires_at' => Carbon::now()->subMinutes(1), // Expired
        ]);

        $response = $this->postJson('/api/user/resend-verification-code', [
            'email' => 'dev.karim12@gmail.com',
        ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertNotEquals('1111', $user->verification_code);
        $this->assertTrue(Carbon::parse($user->verification_code_expires_at)->isFuture());
    }

    public function test_forgot_password_sends_otp()
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Test OTP User',
            'email' => 'dev.karim12@gmail.com',
            'password' => Hash::make('password123'),
            'phone' => '1234567890',
        ]);

        $response = $this->postJson('/api/user/forgot-password', [
            'email' => 'dev.karim12@gmail.com',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'dev.karim12@gmail.com',
        ]);
        
        // Ensure token is numeric (OTP)
        $tokenRecord = DB::table('password_reset_tokens')->where('email', 'dev.karim12@gmail.com')->first();
        $this->assertTrue(is_numeric($tokenRecord->token));
    }

    public function test_reset_password_success()
    {
        $user = User::create([
            'name' => 'Test OTP User',
            'email' => 'dev.karim12@gmail.com',
            'password' => Hash::make('oldpassword'),
            'phone' => '1234567890',
        ]);

        DB::table('password_reset_tokens')->insert([
            'email' => 'dev.karim12@gmail.com',
            'token' => '5678',
            'created_at' => Carbon::now(),
        ]);

        $response = $this->postJson('/api/user/reset-password', [
            'email' => 'dev.karim12@gmail.com',
            'code' => '5678',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200); // 200 OK

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'dev.karim12@gmail.com',
        ]);
    }
}
