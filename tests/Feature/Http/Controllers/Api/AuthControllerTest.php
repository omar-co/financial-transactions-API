<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Client;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_fails_validation_when_the_request_is_empty()
    {
        $this->postJson(route('auth.register'), [

        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Test]
    public function it_fails_validation_when_the_password_confirmation_is_not_present()
    {
        $this->postJson(route('auth.register'), [
            'password' => 'S3cr3t12!',
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
            'password' => 'The password field confirmation does not match.',
        ]);
    }

    #[Test]
    public function it_fails_validation_when_the_password_confirmation_is_empty()
    {
        $this->postJson(route('auth.register'), [
            'password' => 'S3cr3t12!',
            'password_confirmation' => '',
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
            'password' => 'The password field confirmation does not match.',
        ]);
    }

    #[Test]
    public function it_fails_validation_when_the_password_confirmation_is_mismatch()
    {
        $this->postJson(route('auth.register'), [
            'password' => 'S3cr3t12!',
            'password_confirmation' => 'S3cr3',
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
            'password' => 'The password field confirmation does not match.',
        ]);
    }

    #[Test]
    public function it_fails_validation_when_the_email_is_empty()
    {
        $this->postJson(route('auth.register'), [
            'name' => 'John Doe',
            'email' => '',
            'password' => 'S3cr3t12!',
            'password_confirmation' => 'S3cr3t12!',
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
            'email' => 'The email field is required.',
        ]);
    }

    #[Test]
    public function it_fails_validation_when_the_email_is_not_valid()
    {
        $this->postJson(route('auth.register'), [
            'name' => 'John Doe',
            'email' => 'john_doe.com',
            'password' => 'S3cr3t12!',
            'password_confirmation' => 'S3cr3t12!',
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
            'email' => 'The email field must be a valid email address.',
        ]);
    }

    #[Test]
    public function it_fails_validation_when_the_name_is_not_present()
    {
        $this->postJson(route('auth.register'), [
            'email' => 'john_doe.com',
            'password' => 'S3cr3t12!',
            'password_confirmation' => 'S3cr3t12!',
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
            'email' => 'The email field must be a valid email address.',
        ]);
    }

    #[Test]
    public function it_fails_validation_when_the_name_is_empty()
    {
        $this->postJson(route('auth.register'), [
            'name' => '',
            'email' => 'john@doe.com',
            'password' => 'S3cr3t12!',
            'password_confirmation' => 'S3cr3t12!',
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
            'name' => 'The name field is required.',
        ]);
    }

    #[Test]
    public function it_fails_validation_when_the_email_is_already_taken()
    {
        User::factory()->create([
            'email' => 'john@doe.com',
        ]);

        $this->postJson(route('auth.register'), [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'S3cr3t12!',
            'password_confirmation' => 'S3cr3t12!',
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
            'email' => 'The email has already been taken.',
        ]);
    }

    #[Test]
    public function it_registers_a_user()
    {
        Client::factory()->create([
            'id' => config('passport.personal_access_client.id'),
            'secret' => config('passport.personal_access_client.secret'),
            'personal_access_client' => true,
        ]);

        $this->postJson(route('auth.register'), [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'S3cr3t12!',
            'password_confirmation' => 'S3cr3t12!',
        ])->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
        ]);
    }

    #[Test]
    public function it_returns_a_json_with_the_expected_structure()
    {
        Client::factory()->create([
            'id' => config('passport.personal_access_client.id'),
            'secret' => config('passport.personal_access_client.secret'),
            'personal_access_client' => true,
        ]);

        $user = $this->postJson(route('auth.register'), [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'S3cr3t12!',
            'password_confirmation' => 'S3cr3t12!',
        ]);

        $user->assertExactJsonStructure(['data' => ['id', 'name', 'email', 'token']]);
    }
}
