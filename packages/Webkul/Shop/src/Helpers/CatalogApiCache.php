<?php

namespace Webkul\Shop\Helpers;

use Closure;
use Illuminate\Support\Facades\Cache;
use Webkul\Attribute\Repositories\AttributeRepository;

class CatalogApiCache
{
    /**
     * Cache key that stores the current catalog version.
     */
    const VERSION_KEY = 'shop_api_catalog_version';

    /**
     * Lifetime (in seconds) of the catalog version counter.
     *
     * The counter only has to outlive any cached catalog response, so a long,
     * effectively-permanent TTL is used. An explicit (non-null) TTL is what
     * lets `Cache::add()` seed the counter through the store's atomic path -
     * never-expiring entries fall back to a non-atomic check-then-write.
     */
    const VERSION_TTL = 31536000;

    /**
     * Time (in seconds) a cached catalog response is kept.
     */
    const TTL = 3600;

    /**
     * Cache key that stores the codes of the filterable attributes.
     */
    const FILTERABLE_ATTRIBUTES_KEY = 'shop_api_filterable_attribute_codes';

    /**
     * Highest page number that is still cached. Deeper pages are served fresh.
     */
    const MAX_PAGE = 50;

    /**
     * Highest page size that is still cached.
     */
    const MAX_LIMIT = 100;

    /**
     * Maximum number of comma-separated values (ids, option ids, price bounds)
     * a cacheable request may carry across all of its parameters.
     */
    const MAX_VALUES = 10;

    /**
     * Query parameters that may take part in a cache key, per segment, mapped to
     * the rule their value must satisfy. Any other parameter makes the request
     * uncacheable, so arbitrary query strings (tracking tags, bots, typos)
     * cannot mint new cache entries.
     *
     * Products additionally accept the code of every filterable attribute.
     */
    const PARAMS = [
        'products' => [
            'page' => 'page',
            'limit' => 'limit',
            'sort' => 'sort',
            'mode' => 'mode',
            'category_id' => 'ids',
            'new' => 'flag',
            'featured' => 'flag',
            'price' => 'values',
        ],

        'categories' => [
            'page' => 'page',
            'limit' => 'limit',
            'sort' => 'sort',
            'parent_id' => 'ids',
            'only_children' => 'flag',
        ],
    ];

    /**
     * Filterable attribute codes, memoized for the current request.
     */
    protected ?array $filterableAttributeCodes = null;

    /**
     * Create a new helper instance.
     */
    public function __construct(protected AttributeRepository $attributeRepository) {}

    /**
     * Current catalog version.
     *
     * Every product/category change increments this number, which changes the
     * cache key of every catalog response and therefore serves fresh data
     * immediately without having to purge individual cache entries.
     */
    public function version(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }

    /**
     * Bump the catalog version so every cached catalog response is invalidated.
     *
     * The counter is advanced with an atomic increment - seeded by an atomic
     * `add()` on first use - so concurrent invalidations are each counted. A
     * read-then-write would let two simultaneous flushes settle on the same
     * value and lose an update.
     */
    public function flush(): void
    {
        Cache::add(self::VERSION_KEY, 1, self::VERSION_TTL);

        Cache::increment(self::VERSION_KEY);
    }

    /**
     * Catalog responses are only cached for guests. Logged-in customers receive
     * personalised data (wishlist state, customer-group prices) that must not
     * be shared across users.
     */
    public function shouldCache(): bool
    {
        return ! auth()->guard('customer')->check();
    }

    /**
     * Resolve a catalog response, caching it for guests.
     *
     * The callback receives the parameters it must query with. For a cached
     * request these are the normalized parameters the key was built from, so a
     * cached response always matches its key (e.g. `?color=` filters to nothing
     * in the repository, so it must not be stored under the unfiltered key).
     * Uncached requests get their raw parameters, untouched.
     */
    public function remember(string $segment, array $params, Closure $callback): mixed
    {
        if (! $this->shouldCache()) {
            return $callback($params);
        }

        $normalized = $this->normalize($segment, $params);

        if ($normalized === null) {
            return $callback($params);
        }

        return Cache::remember(
            $this->key($segment, $normalized),
            self::TTL,
            fn () => $callback($normalized)
        );
    }

    /**
     * Reduce the parameters to their canonical, cacheable form.
     *
     * Empty values are dropped and the keys sorted, so equivalent requests share
     * one entry. Returns null - meaning "do not cache" - when the request carries
     * an unknown parameter, a malformed or out-of-range value, or more values
     * than `MAX_VALUES`.
     */
    public function normalize(string $segment, array $params): ?array
    {
        if (! isset(self::PARAMS[$segment])) {
            return null;
        }

        $normalized = [];

        $valueCount = 0;

        foreach ($params as $name => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (! is_string($value) && ! is_int($value)) {
                return null;
            }

            $value = (string) $value;

            $rule = $this->rule($segment, (string) $name);

            if (
                ! $rule
                || ! $this->isValid($rule, $value)
            ) {
                return null;
            }

            if (in_array($rule, ['ids', 'values'])) {
                $valueCount += substr_count($value, ',') + 1;

                if ($valueCount > self::MAX_VALUES) {
                    return null;
                }
            }

            /**
             * Id lists are only used in `whereIn` lookups, so their order is
             * irrelevant. Other value lists (e.g. a price range) keep theirs.
             */
            if ($rule === 'ids') {
                $ids = array_unique(explode(',', $value));

                sort($ids, SORT_NUMERIC);

                $value = implode(',', $ids);
            }

            /**
             * The first page is the default one.
             */
            if (
                $rule === 'page'
                && $value === '1'
            ) {
                continue;
            }

            $normalized[$name] = $value;
        }

        ksort($normalized);

        return $normalized;
    }

    /**
     * Rule a parameter's value must satisfy, or null when it is not cacheable.
     */
    protected function rule(string $segment, string $name): ?string
    {
        if (isset(self::PARAMS[$segment][$name])) {
            return self::PARAMS[$segment][$name];
        }

        if (
            $segment === 'products'
            && in_array($name, $this->filterableAttributeCodes(), true)
        ) {
            return 'values';
        }

        return null;
    }

    /**
     * Check a raw parameter value against its rule. Values must already be in
     * canonical form (no leading zeros, no whitespace), so the value the key is
     * built from is exactly the value the query uses.
     */
    protected function isValid(string $rule, string $value): bool
    {
        return match ($rule) {
            'page' => preg_match('/^[1-9]\d{0,3}$/', $value) && (int) $value <= self::MAX_PAGE,
            'limit' => preg_match('/^[1-9]\d{0,3}$/', $value) && (int) $value <= self::MAX_LIMIT,
            'sort' => (bool) preg_match('/^[a-z_]{1,32}(-(asc|desc))?$/', $value),
            'mode' => in_array($value, ['grid', 'list'], true),
            'flag' => in_array($value, ['0', '1'], true),
            'ids' => (bool) preg_match('/^[1-9]\d{0,9}(,[1-9]\d{0,9})*$/', $value),
            'values' => (bool) preg_match('/^\d{1,10}(\.\d{1,4})?(,\d{1,10}(\.\d{1,4})?)*$/', $value),
            default => false,
        };
    }

    /**
     * Codes of the filterable attributes, which the storefront sends as product
     * filter parameters. Kept in a single cache entry so cache hits do not query
     * the attributes table; a stale list only decides whether a request is
     * cached, never what it returns.
     */
    protected function filterableAttributeCodes(): array
    {
        return $this->filterableAttributeCodes ??= Cache::remember(
            self::FILTERABLE_ATTRIBUTES_KEY,
            self::TTL,
            fn () => $this->attributeRepository->getFilterableAttributes()->pluck('code')->all()
        );
    }

    /**
     * Build a version-aware cache key scoped to channel, locale and currency.
     */
    protected function key(string $segment, array $params): string
    {
        return implode(':', [
            'shop_api',
            $this->version(),
            $segment,
            core()->getCurrentChannel()->id,
            core()->getCurrentLocale()->code,
            core()->getCurrentCurrencyCode(),
            md5(json_encode($params)),
        ]);
    }
}
