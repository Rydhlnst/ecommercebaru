<?php

use Webkul\Customer\Models\Customer;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->customer = $this->loginAsCustomer();
});

it('returns customer profile', function () {
    // Act and Assert.
    getJson(route('shop.api.customers.account.profile.index'))
        ->assertOk()
        ->assertJsonStructure([
            'customer' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'gender',
            ],
        ]);
});

it('updates customer profile successfully', function () {
    // Arrange.
    $data = [
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'email' => $this->customer->email,
        'gender' => 'Male',
        'phone' => '+1234567890',
    ];

    // Act and Assert.
    putJson(route('shop.api.customers.account.profile.update'), $data)
        ->assertOk()
        ->assertJson([
            'message' => 'Profile updated successfully.',
        ]);
});

it('validates required fields for profile update', function () {
    // Act and Assert.
    putJson(route('shop.api.customers.account.profile.update'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['first_name', 'last_name', 'gender', 'email', 'phone']);
});

it('validates email uniqueness for profile update', function () {
    // Arrange.
    $otherCustomer = Customer::factory()->create([
        'email' => 'taken@example.com',
    ]);

    $data = [
        'first_name' => $this->customer->first_name,
        'last_name' => $this->customer->last_name,
        'email' => 'taken@example.com',
        'gender' => 'Male',
        'phone' => $this->customer->phone,
    ];

    // Act and Assert.
    putJson(route('shop.api.customers.account.profile.update'), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('validates gender field for profile update', function () {
    // Arrange.
    $data = [
        'first_name' => $this->customer->first_name,
        'last_name' => $this->customer->last_name,
        'email' => $this->customer->email,
        'gender' => 'InvalidGender',
        'phone' => $this->customer->phone,
    ];

    // Act and Assert.
    putJson(route('shop.api.customers.account.profile.update'), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['gender']);
});

it('changes password successfully', function () {
    // Arrange.
    $data = [
        'current_password' => 'password',
        'new_password' => 'new-secret-password',
        'new_password_confirmation' => 'new-secret-password',
    ];

    // Act and Assert.
    putJson(route('shop.api.customers.account.profile.change_password'), $data)
        ->assertOk()
        ->assertJson([
            'message' => 'Password changed successfully.',
        ]);
});

it('rejects wrong current password', function () {
    // Arrange.
    $data = [
        'current_password' => 'wrong-password',
        'new_password' => 'new-secret-password',
        'new_password_confirmation' => 'new-secret-password',
    ];

    // Act and Assert.
    putJson(route('shop.api.customers.account.profile.change_password'), $data)
        ->assertBadRequest()
        ->assertJson([
            'message' => 'Current password is incorrect.',
        ]);
});

it('validates required fields for password change', function () {
    // Act and Assert.
    putJson(route('shop.api.customers.account.profile.change_password'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['current_password', 'new_password']);
});

it('validates password confirmation for password change', function () {
    // Arrange.
    $data = [
        'current_password' => 'password',
        'new_password' => 'new-secret-password',
        'new_password_confirmation' => 'different-password',
    ];

    // Act and Assert.
    putJson(route('shop.api.customers.account.profile.change_password'), $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['new_password']);
});

it('logs out customer successfully', function () {
    // Act and Assert.
    postJson(route('shop.api.customers.session.destroy'))
        ->assertOk()
        ->assertJson([
            'message' => 'Logged out successfully.',
        ]);
});

it('requires authentication for profile access', function () {
    // Arrange.
    auth()->guard('customer')->logout();

    // Act and Assert.
    getJson(route('shop.api.customers.account.profile.index'))
        ->assertUnauthorized();
});
