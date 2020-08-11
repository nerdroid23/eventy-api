<?php

namespace Tests\Feature\Api\Auth;

use App\User;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_cannot_login_without_providing_an_email()
    {
        $data = [
            'email' => '',
            'password' => $this->faker->password(8)
        ];

        $this
            ->postJson('/api/login', $data)
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', __('validation.required', ['attribute' => 'email']));
    }

    /** @test */
    public function user_cannot_login_without_providing_valid_email()
    {
        $data = [
            'email' => 'janed@mail',
            'password' => $this->faker->password(8)
        ];

        $this
            ->postJson('/api/login', $data)
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', __('validation.email', ['attribute' => 'email']));
    }

    /** @test */
    public function user_cannot_login_without_providing_a_password()
    {
        $data = [
            'email' => $this->faker->safeEmail,
            'password' => ''
        ];

        $this
            ->postJson('/api/login', $data)
            ->assertStatus(422)
            ->assertJsonPath('errors.password.0', __('validation.required', ['attribute' => 'password']));
    }

    /** @test */
    public function user_is_assigned_a_token_after_a_successful_login()
    {
        $email = 'jdoe@mail.com';
        $password = 'password';

        $user = factory(User::class)->create([
            'email' => $email,
            'password' => $password
        ]);

        $this
            ->postJson('/api/login', [
                'email' => $user->email,
                'password' => $password
            ])
            ->assertOk();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => 'App\\User',
            'tokenable_id' => $user->id,
        ]);
    }

    /** @test */
    public function users_assigned_token_is_deleted_after_logout()
    {
        $email = 'jdoe@mail.com';
        $password = 'password';

        $user = factory(User::class)->create([
            'email' => $email,
            'password' => $password
        ]);

        Sanctum::actingAs($user, ['*']);

        $this
            ->postJson('/api/logout')
            ->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => 'App\\User',
            'tokenable_id' => $user->id,
        ]);
    }
}
