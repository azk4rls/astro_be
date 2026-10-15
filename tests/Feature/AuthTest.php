<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_without_token_returns_401_with_standard_format(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated. Sertakan bearer token yang valid.',
            ]);
    }

    public function test_login_success_returns_token(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@astronova.co.id',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@astronova.co.id',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'jabatan',
                    ],
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'token_type' => 'Bearer',
                ],
            ]);
    }

    public function test_login_returns_user_role(): void
    {
        User::factory()->create([
            'email' => 'manager@astronova.co.id',
            'password' => bcrypt('password123'),
            'role' => 'atasan',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'manager@astronova.co.id',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.user.role', 'atasan');
    }

    public function test_login_failed_returns_401(): void
    {
        User::factory()->create([
            'email' => 'admin@astronova.co.id',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@astronova.co.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Email atau password salah.',
            ]);
    }

    public function test_login_validation_empty_returns_422(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ])
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_access_protected_with_token_returns_200(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/me');

        $response->assertStatus(200);
    }

    public function test_logout_without_current_token_returns_200(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logout berhasil.',
            ]);
    }

    public function test_logout_then_use_token_returns_401(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/logout');

        $response->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 0);

        // Forget cached guards so the next request re-authenticates the (deleted) token.
        $this->app['auth']->forgetGuards();

        $response2 = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/me');

        $response2->assertStatus(401);
    }

    public function test_test_endpoint_remains_public_returns_200(): void
    {
        $response = $this->getJson('/api/test');

        $response->assertStatus(200);
    }
}
