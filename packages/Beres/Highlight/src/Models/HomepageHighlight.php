<?php

namespace Beres\Highlight\Models;

use Beres\Highlight\Contracts\HomepageHighlight as HomepageHighlightContract;
use Illuminate\Database\Eloquent\Model;
use Webkul\Category\Models\Category;
use Webkul\Product\Models\Product;

class HomepageHighlight extends Model implements HomepageHighlightContract
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'homepage_highlights';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'section',
        'entity_type',
        'entity_id',
        'sort_order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Section constants.
     */
    public const SECTION_FEATURED = 'featured';

    public const SECTION_NEW_ARRIVALS = 'new_arrivals';

    public const SECTION_KITS_BUNDLES = 'kits_bundles';

    public const SECTION_BEST_SELLERS = 'best_sellers';

    public const SECTION_SEEDS = 'seeds_superfoods';

    public const SECTION_CATEGORIES = 'categories';

    /**
     * Product entity type.
     */
    public const TYPE_PRODUCT = 'product';

    /**
     * Category entity type.
     */
    public const TYPE_CATEGORY = 'category';

    /**
     * Get all section definitions.
     */
    public static function getSectionDefinitions(): array
    {
        return [
            self::SECTION_FEATURED => [
                'label' => 'Featured Product',
                'description' => 'Produk unggulan tunggal di bagian atas homepage',
                'type' => self::TYPE_PRODUCT,
                'limit' => 1,
            ],
            self::SECTION_NEW_ARRIVALS => [
                'label' => 'New Arrivals',
                'description' => 'Section produk baru',
                'type' => self::TYPE_PRODUCT,
                'limit' => 4,
            ],
            self::SECTION_KITS_BUNDLES => [
                'label' => 'Kits & Bundles',
                'description' => 'Section paket & bundle',
                'type' => self::TYPE_PRODUCT,
                'limit' => 4,
            ],
            self::SECTION_BEST_SELLERS => [
                'label' => 'Best Sellers',
                'description' => 'Section produk terlaris',
                'type' => self::TYPE_PRODUCT,
                'limit' => null,
            ],
            self::SECTION_SEEDS => [
                'label' => 'Seeds & Superfoods',
                'description' => 'Section seeds & superfoods',
                'type' => self::TYPE_PRODUCT,
                'limit' => 5,
            ],
            self::SECTION_CATEGORIES => [
                'label' => 'Shop by Category',
                'description' => 'Tile kategori di homepage',
                'type' => self::TYPE_CATEGORY,
                'limit' => 8,
            ],
        ];
    }

    /**
     * Polymorphic relation to the pinned product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'entity_id');
    }

    /**
     * Polymorphic relation to the pinned category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'entity_id');
    }
}
