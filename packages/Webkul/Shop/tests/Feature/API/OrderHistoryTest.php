<?php

use Webkul\Customer\Models\Customer;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderItem;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->customer = $this->loginAsCustomer();
});

it('returns list of customer orders', function () {
    // Arrange.
    Order::factory()->create([
        'customer_id' => $this->customer->id,
    ]);

    Order::factory()->create([
        'customer_id' => $this->customer->id,
    ]);

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
    ]);

    // Act and Assert.
    getJson(route('shop.api.customers.account.orders.view', ['id' => $order->id]))
        ->assertNotFound();
});

it('cancels an order successfully', function () {
    // Arrange.
    $order = Order::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => Order::STATUS_PENDING,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => null,
        'type' => 'simple',
        'qty_ordered' => 1,
        'qty_invoiced' => 0,
        'qty_shipped' => 0,
        'qty_refunded' => 0,
        'qty_canceled' => 0,
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
        'status' => Order::STATUS_PENDING,
    ]);

    // Act and Assert.
    postJson(route('shop.api.customers.account.orders.cancel', ['id' => $order->id]))
        ->assertNotFound();
});

it('adds order items to cart for reorder', function () {
    // Arrange.
    $order = Order::factory()->create([
        'customer_id' => $this->customer->id,
        'status' => Order::STATUS_COMPLETED,
    ]);

    // Act and Assert.
    postJson(route('shop.api.customers.account.orders.reorder', ['id' => $order->id]))
        ->assertOk()
        ->assertJson([
            'message' => 'Items have been added to your cart.',
        ]);
});
