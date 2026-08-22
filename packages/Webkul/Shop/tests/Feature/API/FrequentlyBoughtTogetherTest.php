<?php

use Webkul\Faker\Helpers\Product as ProductFaker;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderItem;

use function Pest\Laravel\getJson;

it('returns frequently bought together products', function () {
    // Arrange.
    $productFaker = new ProductFaker;

    $product1 = $productFaker->getSimpleProductFactory()->create();
    $product2 = $productFaker->getSimpleProductFactory()->create();
    $product3 = $productFaker->getSimpleProductFactory()->create();

    // Create an order with products 1 and 2 together.
    $order = Order::factory()->create([
        'customer_id' => null,
        'is_guest' => 1,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product1->id,
        'name' => $product1->name,
        'sku' => $product1->sku,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product2->id,
        'name' => $product2->name,
        'sku' => $product2->sku,
    ]);

    // Act.
    $response = getJson(route('shop.api.products.frequently_bought_together.index', ['id' => $product1->id]))
        ->assertOk()
        ->collect();

    // Assert.
    expect($response['data'])->not->toBeEmpty();
    expect($response['data'][0]['id'])->toBe($product2->id);
});

it('returns empty for product with no co-occurring products', function () {
    // Arrange.
    $productFaker = new ProductFaker;

    $product = $productFaker->getSimpleProductFactory()->create();

    // Act.
    $response = getJson(route('shop.api.products.frequently_bought_together.index', ['id' => $product->id]))
        ->assertOk()
        ->collect();

    // Assert.
    expect($response['data'])->toBeEmpty();
});

it('excludes current product from results', function () {
    // Arrange.
    $productFaker = new ProductFaker;

    $product = $productFaker->getSimpleProductFactory()->create();

    // Create an order with only this product.
    $order = Order::factory()->create([
        'customer_id' => null,
        'is_guest' => 1,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'name' => $product->name,
        'sku' => $product->sku,
    ]);

    // Act.
    $response = getJson(route('shop.api.products.frequently_bought_together.index', ['id' => $product->id]))
        ->assertOk()
        ->collect();

    // Assert.
    expect($response['data'])->toBeEmpty();
});

it('returns 404 for non-existent product', function () {
    // Act and Assert.
    getJson(route('shop.api.products.frequently_bought_together.index', ['id' => 99999]))
        ->assertNotFound();
});
