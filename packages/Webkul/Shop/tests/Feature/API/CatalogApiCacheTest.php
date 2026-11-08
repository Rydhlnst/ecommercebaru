<?php

use Webkul\Faker\Helpers\Category as CategoryFaker;
use Webkul\Faker\Helpers\Product as ProductFaker;
use Webkul\Shop\Helpers\CatalogApiCache;
use Webkul\Shop\Listeners\CatalogCache;
use Webkul\Shop\Providers\EventServiceProvider;

use function Pest\Laravel\getJson;

it('increments the catalog version when flushed', function () {
    $cache = app(CatalogApiCache::class);

    $version = $cache->version();

    $cache->flush();

    expect($cache->version())->toBe($version + 1);
});

it('counts every flush so invalidations are not collapsed', function () {
    $cache = app(CatalogApiCache::class);

    $version = $cache->version();

    $cache->flush();
    $cache->flush();
    $cache->flush();

    expect($cache->version())->toBe($version + 3);
});

it('caches a value until the catalog version is bumped', function () {
    $cache = app(CatalogApiCache::class);

    $first = $cache->remember('products', ['limit' => 10], fn () => 'first');
    $second = $cache->remember('products', ['limit' => 10], fn () => 'second');

    expect($first)->toBe('first')
        ->and($second)->toBe('first');

    $cache->flush();

    $third = $cache->remember('products', ['limit' => 10], fn () => 'third');

    expect($third)->toBe('third');
});

it('does not cache catalog responses for logged-in customers', function () {
    $cache = app(CatalogApiCache::class);

    expect($cache->shouldCache())->toBeTrue();

    $this->loginAsCustomer();

    expect($cache->shouldCache())->toBeFalse();

    $resolved = $cache->remember('products', [], fn () => 'fresh-each-time');

    expect($resolved)->toBe('fresh-each-time');
});

it('bypasses the cache for requests with unknown params', function (string $segment, array $params) {
    $cache = app(CatalogApiCache::class);

    $calls = 0;

    $resolve = function () use (&$calls) {
        return ++$calls;
    };

    $cache->remember($segment, $params, $resolve);
    $cache->remember($segment, $params, $resolve);

    expect($cache->normalize($segment, $params))->toBeNull()
        ->and($calls)->toBe(2);
})->with([
    'tracking tag' => ['products', ['limit' => '12', 'utm_source' => 'bot']],
    'cache buster' => ['products', ['_' => '1729300000']],
    'search suggest flag' => ['products', ['suggest' => '0']],
    'non-filterable attribute' => ['products', ['url_key' => 'shirt']],
    'category text search' => ['categories', ['name' => 'shirts']],
    'category status override' => ['categories', ['status' => '0']],
    'unknown segment' => ['reviews', ['limit' => '10']],
]);

it('bypasses the cache for malformed or out-of-range values', function (array $params) {
    expect(app(CatalogApiCache::class)->normalize('products', $params))->toBeNull();
})->with([
    'page beyond the cached range' => [['page' => (string) (CatalogApiCache::MAX_PAGE + 1)]],
    'zero page' => [['page' => '0']],
    'page with a leading zero' => [['page' => '02']],
    'limit beyond the cached range' => [['limit' => (string) (CatalogApiCache::MAX_LIMIT + 1)]],
    'non-numeric limit' => [['limit' => 'all']],
    'array value' => [['category_id' => ['1', '2']]],
    'non-numeric id' => [['category_id' => '1 OR 1=1']],
    'unknown mode' => [['mode' => 'table']],
    'free-text sort' => [['sort' => 'price; drop']],
    'non-numeric filter value' => [['color' => 'red']],
]);

it('bypasses the cache when a request carries too many values', function () {
    $cache = app(CatalogApiCache::class);

    $ids = implode(',', range(1, CatalogApiCache::MAX_VALUES));

    expect($cache->normalize('products', ['category_id' => $ids]))->not->toBeNull()
        ->and($cache->normalize('products', ['category_id' => $ids.','.(CatalogApiCache::MAX_VALUES + 1)]))->toBeNull()
        ->and($cache->normalize('products', ['category_id' => $ids, 'color' => '1']))->toBeNull();
});

it('accepts the storefront toolbar params and filterable attribute codes', function () {
    $normalized = app(CatalogApiCache::class)->normalize('products', [
        'category_id' => '5',
        'sort' => 'price-desc',
        'limit' => '24',
        'mode' => 'grid',
        'page' => '2',
        'color' => '1,3',
        'price' => '10.5,250',
        'new' => '1',
    ]);

    expect($normalized)->toBe([
        'category_id' => '5',
        'color' => '1,3',
        'limit' => '24',
        'mode' => 'grid',
        'new' => '1',
        'page' => '2',
        'price' => '10.5,250',
        'sort' => 'price-desc',
    ]);
});

it('builds the same key regardless of param order', function () {
    $cache = app(CatalogApiCache::class);

    $first = $cache->remember('products', ['limit' => '12', 'sort' => 'name-asc', 'category_id' => '3,1'], fn () => 'first');
    $second = $cache->remember('products', ['category_id' => '1,3', 'sort' => 'name-asc', 'limit' => '12'], fn () => 'second');

    expect($first)->toBe('first')
        ->and($second)->toBe('first');
});

it('ignores empty params and the default page when building the key', function () {
    $cache = app(CatalogApiCache::class);

    $first = $cache->remember('products', ['limit' => '12'], fn () => 'first');
    $second = $cache->remember('products', ['limit' => '12', 'color' => '', 'price' => null, 'page' => '1'], fn () => 'second');

    expect($first)->toBe('first')
        ->and($second)->toBe('first');
});

it('hands the normalized params to the callback so the response matches its key', function () {
    $received = app(CatalogApiCache::class)->remember(
        'products',
        ['sort' => 'name-asc', 'color' => '', 'category_id' => '3,1'],
        fn (array $params) => $params
    );

    expect($received)->toBe([
        'category_id' => '1,3',
        'sort' => 'name-asc',
    ]);
});

it('hands the raw params to the callback when the request is not cached', function () {
    $params = ['utm_source' => 'bot', 'color' => ''];

    $received = app(CatalogApiCache::class)->remember('products', $params, fn (array $params) => $params);

    expect($received)->toBe($params);
});

it('does not let an empty filter param poison the cached products listing', function () {
    (new ProductFaker)->getSimpleProductFactory()->create();

    $withEmptyFilter = getJson(route('shop.api.products.index').'?color=')
        ->assertOk()
        ->json('data');

    $plain = getJson(route('shop.api.products.index'))
        ->assertOk()
        ->json('data');

    expect($withEmptyFilter)->not->toBeEmpty()
        ->and($plain)->toBe($withEmptyFilter);
});

it('invalidates the catalog cache when a product is updated', function () {
    $product = (new ProductFaker)->getSimpleProductFactory()->create();

    $cache = app(CatalogApiCache::class);

    $version = $cache->version();

    event('catalog.product.update.after', $product);

    expect($cache->version())->toBe($version + 1);
});

it('invalidates the catalog cache when a category is updated', function () {
    $category = (new CategoryFaker)->factory()->create();

    $cache = app(CatalogApiCache::class);

    $version = $cache->version();

    event('catalog.category.update.after', $category);

    expect($cache->version())->toBe($version + 1);
});

it('wires catalog cache invalidation for every event that can change a cached listing', function (string $event) {
    $listens = (new EventServiceProvider(app()))->listens();

    expect($listens)->toHaveKey($event)
        ->and($listens[$event])->toContain([CatalogCache::class, 'flush']);
})->with([
    'catalog.product.create.after',
    'catalog.product.update.after',
    'catalog.product.delete.before',
    'catalog.category.create.after',
    'catalog.category.update.after',
    'catalog.category.delete.before',
    'customer.review.update.after',
    'customer.review.delete.before',
    'checkout.order.save.after',
    'sales.order.cancel.after',
    'sales.refund.save.after',
]);

it('sends a public, cookie-varying cache-control header on the products listing for guests', function () {
    getJson(route('shop.api.products.index'))
        ->assertOk()
        ->assertHeader('Cache-Control', 'max-age=60, public')
        ->assertHeader('Vary', 'Cookie');
});

it('sends a public, cookie-varying cache-control header on the categories listing for guests', function () {
    getJson(route('shop.api.categories.index'))
        ->assertOk()
        ->assertHeader('Cache-Control', 'max-age=60, public')
        ->assertHeader('Vary', 'Cookie');
});

it('sends a private cache-control header on the products listing for customers', function () {
    $this->loginAsCustomer();

    getJson(route('shop.api.products.index'))
        ->assertOk()
        ->assertHeader('Cache-Control', 'no-cache, private');
});
