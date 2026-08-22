<?php

use Webkul\Customer\Repositories\CustomerRepository;

use function Pest\Laravel\postJson;

it('registers a new customer successfully', function () {
    // Arrange.
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ];

    // Act and Assert.
    postJson(route('shop.api.customers.register'), $data)
        ->assertCreated()
        ->assertJsonStructure([
            'message',
            'customer' => [
                'id',
                'first_name',
                'last_name',
                'email',
            ],
        ]);
});

it('validates required fields for registration', function () {
    // Act and Assert.
    postJson(route('shop.api.customers.register'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'password']);
});

it('validates email format for registration', function () {
    // Arrange.
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'invalid-email',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ];

    // Act and Assert.
    postJson(route('shop.api.customers.register'), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('validates unique email for registration', function () {
    // Arrange.
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'existing@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ];

    // Create a customer with this email.
    $this->app->make(CustomerRepository::class)->create([
        'first_name' => 'Existing',
        'last_name' => 'Customer',
        'email' => 'existing@example.com',
        'password' => bcrypt('secret123'),
        'channel_id' => core()->getCurrentChannel()->id,
        'customer_group_id' => 1,
    ]);

    // Act and Assert.
    postJson(route('shop.api.customers.register'), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('validates password confirmation for registration', function () {
    // Arrange.
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john2@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'different-password',
    ];

    // Act and Assert.
    postJson(route('shop.api.customers.register'), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('validates minimum password length for registration', function () {
    // Arrange.
    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john3@example.com',
        'password' => '12345',
        'password_confirmation' => '12345',
    ];

    // Act and Assert.
    postJson(route('shop.api.customers.register'), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});
