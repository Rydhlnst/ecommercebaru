<?php

use Illuminate\Support\Facades\DB;
use Webkul\Customer\Models\Customer;
use Webkul\Faker\Helpers\Product as ProductFaker;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderItem;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->customer = $this->loginAsCustomer();
});

it('returns list of customer orders', function () {
    // Arrange.
    foreach (range(1, 2) as $i) {
        Order::factory()->create([
            'customer_id' => $this->customer->id,
            'customer_email' => $this->customer->email,
            'customer_first_name' => $this->customer->first_name,
            'customer_last_name' => $this->customer->last_name,
            'increment_id' => now()->timestamp.$i,
        ]);
    }

    // Act and Assert.
    getJson(route('shop.api.customers.account.orders.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'increment_id',
                    'status',
                    'grand_total',
                ],
            ],
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
        ]);
});

it('returns order detail for valid order', function () {
    // Arrange.
    $order = Order::factory()->create([
        'customer_id' => $this->customer->id,
        'customer_email' => $this->customer->email,
        'customer_first_name' => $this->customer->first_name,
        'customer_last_name' => $this->customer->last_name,
        'increment_id' => now()->timestamp.rand(100, 999),
    ]);

    // Act and Assert.
    getJson(route('shop.api.customers.account.orders.view', ['id' => $order->id]))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'increment_id',
                'status',
                'grand_total',
                'items',
            ],
        ]);
});

it('returns 404 for non-existent order', function () {
    // Act and Assert.
    getJson(route('shop.api.customers.account.orders.view', ['id' => 99999]))
        ->assertNotFound();
});

it('returns 404 when viewing other customer order', function () {
    // Arrange.
    $otherCustomer = Customer::factory()->create();

    $order = Order::factory()->create([
        'customer_id' => $otherCustomer->id,
        'customer_email' => $otherCustomer->email,
        'customer_first_name' => $otherCustomer->first_name,
        'customer_last_name' => $otherCustomer->last_name,
        'increment_id' => now()->timestamp.rand(100, 999),
    ]);

    // Act and Assert.
    getJson(route('shop.api.customers.account.orders.view', ['id' => $order->id]))
        ->assertNotFound();
});

it('cancels an order successfully', function () {
    // Arrange.
    $product = (new ProductFaker)
        ->getSimpleProductFactory()
        ->create();

    $order = Order::factory()->create([
        'customer_id' => $this->customer->id,
        'customer_email' => $this->customer->email,
        'customer_first_name' => $this->customer->first_name,
        'customer_last_name' => $this->customer->last_name,
        'status' => Order::STATUS_PENDING,
        'increment_id' => now()->timestamp.rand(100, 999),
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'type' => 'simple',
        'name' => $product->name,
        'sku' => $product->sku,
    ]);

    // Act and Assert.
    postJson(route('shop.api.customers.account.orders.cancel', ['id' => $order->id]))
        ->assertOk()
        ->assertJson([
            'message' => 'Order has been canceled successfully.',
        ]);
});

it('cannot cancel other customer order', function () {
    // Arrange.
    $otherCustomer = Customer::factory()->create();

    $order = Order::factory()->create([
        'customer_id' => $otherCustomer->id,
        'customer_email' => $otherCustomer->email,
        'customer_first_name' => $otherCustomer->first_name,
        'customer_last_name' => $otherCustomer->last_name,
        'increment_id' => now()->timestamp.rand(100, 999),
    ]);

    // Act and Assert.
    postJson(route('shop.api.customers.account.orders.cancel', ['id' => $order->id]))
        ->assertNotFound();
});

it('adds order items to cart for reorder', function () {
    // Arrange.
    $product = (new ProductFaker)
        ->getSimpleProductFactory()
        ->create();

    $order = Order::factory()->create([
        'customer_id' => $this->customer->id,
        'customer_email' => $this->customer->email,
        'customer_first_name' => $this->customer->first_name,
        'customer_last_name' => $this->customer->last_name,
        'status' => Order::STATUS_COMPLETED,
        'increment_id' => now()->timestamp.rand(100, 999),
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'type' => 'simple',
        'name' => $product->name,
        'sku' => $product->sku,
    ]);

    // Act and Assert.
    postJson(route('shop.api.customers.account.orders.reorder', ['id' => $order->id]))
        ->assertOk()
        ->assertJson([
            'message' => 'Items have been added to your cart.',
        ]);
});

it('does not leave partial cart items when a reorder item cannot be added', function () {
    // Arrange.
    $product = (new ProductFaker)
        ->getSimpleProductFactory()
        ->create();

    $order = Order::factory()->create([
        'customer_id' => $this->customer->id,
        'customer_email' => $this->customer->email,
        'customer_first_name' => $this->customer->first_name,
        'customer_last_name' => $this->customer->last_name,
        'status' => Order::STATUS_COMPLETED,
        'increment_id' => now()->timestamp.rand(100, 999),
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'type' => 'simple',
        'name' => $product->name,
        'sku' => $product->sku,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => 999999,
        'type' => 'simple',
        'name' => 'Unavailable product',
        'sku' => 'unavailable-product',
    ]);

    // Act and Assert.
    postJson(route('shop.api.customers.account.orders.reorder', ['id' => $order->id]))
        ->assertBadRequest()
        ->assertJson([
            'message' => 'Failed to add product to cart: Unavailable product',
        ]);

    expect(DB::table('cart')
        ->where('customer_id', $this->customer->id)
        ->where('is_active', 1)
        ->exists())->toBeFalse();
});
