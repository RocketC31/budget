<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserVerificationTest extends TestCase
{
    public function testValidToken(): void
    {
        $token = 123;

        User::factory()->create([
            'verification_token' => $token
        ]);

        $response = $this
            ->followingRedirects()
            ->get('/verify/' . $token);

        $response
            ->assertStatus(200);
    }

    public function testInvalidToken(): void
    {
        $token = 456;

        $response = $this
            ->followingRedirects()
            ->get('/verify/' . $token);

        $response
            ->assertStatus(200);
    }
}
