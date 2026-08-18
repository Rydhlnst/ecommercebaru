<form action="{{ $filterAction }}" method="GET" class="catalog-filter-form">
    @if(isset($query) && $query !== '')
        <input type="hidden" name="query" value="{{ $query }}">
    @endif

    <div class="catalog-filter-group">
        <div class="catalog-filter-heading"><span>Availability</span><span aria-hidden="true">⌃</span></div>
        <label class="catalog-filter-label mt-5">
            <input type="checkbox" name="in_stock" value="1" class="catalog-filter-switch" @checked(request()->boolean('in_stock'))>
            <span>In stock only</span>
        </label>
    </div>

    <div class="catalog-filter-group">
        <div class="catalog-filter-heading"><span>Price</span><span aria-hidden="true">⌃</span></div>
        <p class="catalog-filter-price-note">Harga tertinggi saat ini adalah <strong>Rp {{ number_format($filterMaxPrice, 0, ',', '.') }}</strong></p>
        <div class="catalog-filter-price-fields">
            <input type="number" name="min_price" min="0" step="1000" value="{{ request()->query('min_price') }}" placeholder="Dari" aria-label="Harga minimum">
            <input type="number" name="max_price" min="0" step="1000" value="{{ request()->query('max_price') }}" placeholder="Sampai" aria-label="Harga maksimum">
        </div>
    </div>

    <div class="catalog-filter-group">
        <label for="catalog-sort" class="catalog-filter-heading"><span>Sort by</span></label>
        <select id="catalog-sort" name="sort" class="mt-4 w-full rounded-xl border border-[#DDE7D9] bg-white px-3 py-3 text-sm text-[#171717] focus:border-[#2D5A27] focus:outline-none">
            <option value="featured" @selected($filterSort === 'featured')>Featured</option>
            <option value="newest" @selected($filterSort === 'newest')>Newest</option>
            <option value="price_asc" @selected($filterSort === 'price_asc')>Price: Low to high</option>
            <option value="price_desc" @selected($filterSort === 'price_desc')>Price: High to low</option>
        </select>
    </div>

    <button type="submit" class="catalog-filter-submit">Apply filters</button>
</form>
