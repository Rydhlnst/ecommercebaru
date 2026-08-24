<?php

use Illuminate\Support\Facades\Queue;
use Webkul\Attribute\Models\Attribute;
use Webkul\Core\Models\Channel;
use Webkul\Faker\Helpers\Product as ProductFaker;
use Webkul\Product\Listeners\Order as OrderListener;
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
        'increment_id' => now()->timestamp.rand(100, 999),
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
        'increment_id' => now()->timestamp.rand(100, 999),
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

it('excludes canceled orders from recommendations', function () {
    // Arrange.
    $productFaker = new ProductFaker;

    $product1 = $productFaker->getSimpleProductFactory()->create();
    $product2 = $productFaker->getSimpleProductFactory()->create();

    $order = Order::factory()->create([
        'customer_id' => null,
        'is_guest' => 1,
        'increment_id' => now()->timestamp.rand(100, 999),
        'status' => Order::STATUS_CANCELED,
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
    expect($response['data'])->toBeEmpty();
});

it('isolates recommendations to the active channel', function () {
    // Arrange.
    $productFaker = new ProductFaker;

    $product1 = $productFaker->getSimpleProductFactory()->create();
    $product2 = $productFaker->getSimpleProductFactory()->create();
    $otherChannel = Channel::factory()->create();

    $order = Order::factory()->create([
        'channel_id' => $otherChannel->id,
        'customer_id' => null,
        'is_guest' => 1,
        'increment_id' => now()->timestamp.rand(100, 999),
    ]);

    foreach ([$product1, $product2] as $product) {
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
        ]);
    }

    // Act.
    $response = getJson(route('shop.api.products.frequently_bought_together.index', ['id' => $product1->id]))
        ->assertOk()
        ->collect();

    // Assert.
    expect($response['data'])->toBeEmpty();
});

it('excludes inactive and hidden recommendations', function () {
    // Arrange.
    $productFaker = new ProductFaker;
    $sourceProduct = $productFaker->getSimpleProductFactory()->create();
    $inactiveProduct = $productFaker->getSimpleProductFactory()->create();
    $hiddenProduct = $productFaker->getSimpleProductFactory()->create();

    foreach ([$inactiveProduct, $hiddenProduct] as $candidateProduct) {
        $order = Order::factory()->create([
            'customer_id' => null,
            'is_guest' => 1,
            'increment_id' => now()->timestamp.rand(100, 999),
        ]);

        foreach ([$sourceProduct, $candidateProduct] as $product) {
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
            ]);
        }
    }

    $inactiveProduct->attribute_values()
        ->where('attribute_id', Attribute::where('code', 'status')->value('id'))
        ->update(['boolean_value' => false]);

    $hiddenProduct->attribute_values()
        ->where('attribute_id', Attribute::where('code', 'visible_individually')->value('id'))
        ->update(['boolean_value' => false]);

    // Act.
    $response = getJson(route('shop.api.products.frequently_bought_together.index', ['id' => $sourceProduct->id]))
        ->assertOk()
        ->collect();

    // Assert.
    expect($response['data'])->toBeEmpty();
});

it('invalidates a cached recommendation when its order is canceled', function () {
    // Arrange.
    Queue::fake();

    $productFaker = new ProductFaker;
    $product1 = $productFaker->getSimpleProductFactory()->create();
    $product2 = $productFaker->getSimpleProductFactory()->create();

    $order = Order::factory()->create([
        'customer_id' => null,
        'is_guest' => 1,
        'increment_id' => now()->timestamp.rand(100, 999),
    ]);

    foreach ([$product1, $product2] as $product) {
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
        ]);
    }

    getJson(route('shop.api.products.frequently_bought_together.index', ['id' => $product1->id]))
        ->assertOk()
        ->assertJsonPath('data.0.id', $product2->id);

    $order->update(['status' => Order::STATUS_CANCELED]);
    app(OrderListener::class)->afterCancelOrCreate($order);

    // Act.
    $response = getJson(route('shop.api.products.frequently_bought_together.index', ['id' => $product1->id]))
        ->assertOk()
        ->collect();

    // Assert.
    expect($response['data'])->toBeEmpty();
});

it('invalidates a cached recommendation when a new order is created', function () {
    // Arrange.
    Queue::fake();

    $productFaker = new ProductFaker;
    $product1 = $productFaker->getSimpleProductFactory()->create();
    $product2 = $productFaker->getSimpleProductFactory()->create();
    $product3 = $productFaker->getSimpleProductFactory()->create();

    $firstOrder = Order::factory()->create([
        'customer_id' => null,
        'is_guest' => 1,
        'increment_id' => now()->timestamp.rand(100, 999),
    ]);

    foreach ([$product1, $product2] as $product) {
        OrderItem::factory()->create([
            'order_id' => $firstOrder->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
        ]);
    }

    getJson(route('shop.api.products.frequently_bought_together.index', ['id' => $product1->id]))
        ->assertOk()
        ->assertJsonPath('data.0.id', $product2->id);

    $newOrder = Order::factory()->create([
        'customer_id' => null,
        'is_guest' => 1,
        'increment_id' => now()->timestamp.rand(100, 999),
    ]);

    foreach ([$product1, $product3] as $product) {
        OrderItem::factory()->create([
            'order_id' => $newOrder->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
        ]);
    }

    app(OrderListener::class)->afterCancelOrCreate($newOrder);

    // Act and Assert.
    getJson(route('shop.api.products.frequently_bought_together.index', ['id' => $product1->id]))
        ->assertOk()
        ->assertJsonFragment(['id' => $product3->id]);
});

it('returns 404 for non-existent product', function () {
    // Act and Assert.
    getJson(route('shop.api.products.frequently_bought_together.index', ['id' => 99999]))
        ->assertNotFound();
});
