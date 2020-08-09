<?php

namespace Tests\Feature\Api\Auth;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_register()
    {
        $data = [
            'name' => $name = $this->faker->name,
            'email' => $email = $this->faker->safeEmail,
            'password' => $password = $this->faker->password(8),
            'password_confirmation' => $password,
        ];

        $this
            ->postJson('/api/register', $data)
            ->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    /** @test */
    public function user_cannot_register_with_already_registered_email()
    {
        $oldUser = factory(User::class)->create();

        $data = [
            'name' => $name = $this->faker->name,
            'email' => $email = $oldUser->email,
            'password' => $password = $this->faker->password(8),
            'password_confirmation' => $password,
        ];

        $this
            ->postJson('/api/register', $data)
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', __('validation.unique', ['attribute' => 'email']));
    }

    /** @test */
    public function user_cannot_register_without_providing_a_name()
    {
        $data = [
            'email' => $email = $this->faker->email,
            'password' => $password = $this->faker->password(8),
            'password_confirmation' => $password,
        ];

        $this
            ->postJson('/api/register', $data)
            ->assertStatus(422)
            ->assertJsonPath('errors.name.0', __('validation.required', ['attribute' => 'name']));
    }

    /** @test */
    public function user_cannot_register_without_providing_an_email()
    {
        $data = [
            'name' => $name = $this->faker->name,
            'password' => $password = $this->faker->password(8),
            'password_confirmation' => $password,
        ];

        $this
            ->postJson('/api/register', $data)
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', __('validation.required', ['attribute' => 'email']));
    }

    /** @test */
    public function user_cannot_register_without_providing_a_valid_email()
    {
        $data = [
            'name' => $name = $this->faker->name,
            'email' => 'hello',
            'password' => $password = $this->faker->password(8),
            'password_confirmation' => $password,
        ];

        $this
            ->postJson('/api/register', $data)
            ->assertStatus(422)
            ->assertJsonPath('errors.email.0', __('validation.email', ['attribute' => 'email']));
    }

    /** @test */
    public function user_cannot_register_without_providing_a_password()
    {
        $data = [
            'name' => $name = $this->faker->name,
            'email' => $email = $this->faker->safeEmail,
        ];

        $this
            ->postJson('/api/register', $data)
            ->assertStatus(422)
            ->assertJsonPath('errors.password.0', __('validation.required', ['attribute' => 'password']));
    }

    /** @test */
    public function user_cannot_register_without_providing_password_confirmation()
    {
        $data = [
            'name' => $name = $this->faker->name,
            'email' => $email = $this->faker->safeEmail,
            'password' => $password = $this->faker->password(8),
        ];

        $this
            ->postJson('/api/register', $data)
            ->assertStatus(422)
            ->assertJsonPath('errors.password.0', __('validation.confirmed', ['attribute' => 'password']));
    }

    /** @test */
    public function user_cannot_register_without_providing_password_with_8_characters()
    {
        $data = [
            'name' => $name = $this->faker->name,
            'email' => $email = $this->faker->safeEmail,
            'password' => $password = '12345',
            'password_confirmation' => $password,
        ];

        $this
            ->postJson('/api/register', $data)
            ->assertStatus(422)
            ->assertJsonPath('errors.password.0', __('validation.min.string', ['attribute' => 'password', 'min' => 8]));
    }
}
