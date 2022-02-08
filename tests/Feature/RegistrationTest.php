<?php

namespace Tests\Feature;

use App\Genre;
use Tests\TestCase;
use App\Fakes\FakePaymentGateway;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        Role::create(['guard_name' => 'web', 'name' => 'standard']);
        Role::create(['guard_name' => 'web', 'name' => 'artist']);
        Role::create(['guard_name' => 'web', 'name' => 'pro']);

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();
    }

    /**
     * @test
     */
    public function a_music_fan_can_create_an_account()
    {
        //        $paymentGateway = new FakePaymentGateway();
        //        $this->app->instance(\Braintree\Gateway::class, $paymentGateway);

        $this->withoutExceptionHandling();
        $data = [
            'personal' => [
                'username' => 'test',
                'firstname' => 'test',
                'surname' => 'user',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ],
            'interests' => [
                'genres' => factory(Genre::class, 2)->create()
            ]
        ];

        $this->postJson('/api/auth/register/standard', $data)
            ->assertStatus(201)
            ->assertJsonFragment([
                'first_name' => 'test',
                'last_name' => 'user',
                'email' => 'test@example.com',
                'name' => 'test',
            ]);

        $this->assertDatabaseHas('users', [
            'first_name' => 'test',
            'last_name' => 'user',
            'email' => 'test@example.com',
            'name' => 'test',
        ]);
    }

    /**
     * @test
     */
    public function a_pro_standard_can_create_an_account()
    {
        //        $paymentGateway = new FakePaymentGateway();
        //        $this->app->instance(\Braintree\Gateway::class, $paymentGateway);

        $this->withoutExceptionHandling();

        $data = [
            'personal' => [
                'username' => 'test',
                'firstname' => 'test',
                'surname' => 'user',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ],
            'artist' => [
                'username' => 'test',
                'genres' => factory(Genre::class, 2)->create(),
            ],
            'social' => [
                'website' => 'website',
                'facebook' => 'https://facebook.com',
                'twitter' => 'https://twitter.com',
                'youtube' => 'https://youtube.com',
                'soundcloud' => 'https://soundcloud.com'
            ],
            'interests' => [
                'genres' => factory(Genre::class, 2)->create()
            ]
        ];

        $this->postJson('/api/auth/register/artist', $data)
            ->assertStatus(201)
            ->assertJsonFragment([
                'first_name' => 'test',
                'last_name' => 'user',
                'email' => 'test@example.com',
                'name' => 'test',
            ]);

        $this->assertDatabaseHas('users', [
            'first_name' => 'test',
            'last_name' => 'user',
            'email' => 'test@example.com',
            'name' => 'test',
            //            'braintree' => 1234,
            'social_web' => 'website',
            'social_facebook' => 'https://facebook.com',
            'social_twitter' => 'https://twitter.com',
            'social_youtube' => 'https://youtube.com',
        ]);
    }

    /**
     * @test
     */
    public function a_pro_premium_can_create_an_account()
    {
        //        $paymentGateway = new FakePaymentGateway();
        //        $this->app->instance(\Braintree\Gateway::class, $paymentGateway);

        $this->withoutExceptionHandling();
        $data = [
            'personal' => [
                'username' => 'test',
                'firstname' => 'test',
                'surname' => 'user',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ],
            'artist' => [
                'username' => 'test',
                'genres' => factory(Genre::class, 2)->create(),
            ],
            'social' => [
                'website' => 'website',
                'facebook' => 'https://facebook.com',
                'twitter' => 'https://twitter.com',
                'youtube' => 'https://youtube.com',
            ],
            'interests' => [
                'genres' => factory(Genre::class, 2)->create()
            ]
        ];

        $this->postJson('/api/auth/register/pro', $data)
            ->assertStatus(201)
            ->assertJsonFragment([
                'first_name' => 'test',
                'last_name' => 'user',
                'email' => 'test@example.com',
                'name' => 'test',
            ]);

        $this->assertDatabaseHas('users', [
            'first_name' => 'test',
            'last_name' => 'user',
            'email' => 'test@example.com',
            'name' => 'test',
            //            'braintree' => 1234,
            'social_web' => 'website',
            'social_facebook' => 'https://facebook.com',
            'social_twitter' => 'https://twitter.com',
            'social_youtube' => 'https://youtube.com',
        ]);
    }
}