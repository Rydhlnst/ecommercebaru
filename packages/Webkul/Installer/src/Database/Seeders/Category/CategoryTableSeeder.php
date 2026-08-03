<?php

namespace Webkul\Installer\Database\Seeders\Category;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryTableSeeder extends Seeder
{
    /**
     * Base path for the images.
     */
    const BASE_PATH = 'packages/Webkul/Installer/src/Resources/assets/images/seeders/category/';

    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::table('categories')->delete();

        DB::table('category_translations')->delete();

        $now = Carbon::now();

        $defaultLocale = $parameters['default_locale'] ?? config('app.locale');

        DB::table('categories')->insert([
            [
                'id' => 1,
                'position' => 1,
                'logo_path' => null,
                'status' => 1,
                '_lft' => 1,
                '_rgt' => 16,
                'parent_id' => null,
                'banner_path' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $locales = $parameters['allowed_locales'] ?? [$defaultLocale];

        foreach ($locales as $locale) {
            DB::table('category_translations')->insert([
                [
                    'name' => trans('installer::app.seeders.category.categories.name', [], $locale),
                    'slug' => 'root',
                    'description' => trans('installer::app.seeders.category.categories.description', [], $locale),
                    'meta_title' => '',
                    'meta_description' => '',
                    'meta_keywords' => '',
                    'category_id' => '1',
                    'locale' => $locale,
                ],
            ]);
        }
    }

    /**
     * Create Sample Categories — Kitchen Needs store.
     *
     * @return void
     */
    public function sampleCategories(array $parameters = [])
    {
        $defaultLocale = $parameters['default_locale'] ?? config('app.locale');

        $now = Carbon::now();

        $locales = $parameters['allowed_locales'] ?? [$defaultLocale];

        // Clear before re-seeding so this method is idempotent
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('category_filterable_attributes')->delete();
        DB::table('category_translations')->delete();
        DB::table('product_categories')->delete();
        DB::table('categories')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Insert root category
        DB::table('categories')->insert([[
            'id'          => 1,
            'position'    => 1,
            'logo_path'   => null,
            'status'      => 1,
            '_lft'        => 1,
            '_rgt'        => 16,
            'parent_id'   => null,
            'banner_path' => null,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]]);

        // 7 top-level categories for Kitchen Needs store
        $categories = [
            [
                'id' => 2, 'position' => 1, 'parent_id' => 1,
                '_lft' => 2, '_rgt' => 3,
            ],
            [
                'id' => 3, 'position' => 2, 'parent_id' => 1,
                '_lft' => 4, '_rgt' => 5,
            ],
            [
                'id' => 4, 'position' => 3, 'parent_id' => 1,
                '_lft' => 6, '_rgt' => 7,
            ],
            [
                'id' => 5, 'position' => 4, 'parent_id' => 1,
                '_lft' => 8, '_rgt' => 9,
            ],
            [
                'id' => 6, 'position' => 5, 'parent_id' => 1,
                '_lft' => 10, '_rgt' => 11,
            ],
            [
                'id' => 7, 'position' => 6, 'parent_id' => 1,
                '_lft' => 12, '_rgt' => 13,
            ],
            [
                'id' => 8, 'position' => 7, 'parent_id' => 1,
                '_lft' => 14, '_rgt' => 15,
            ],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->insert(array_merge($cat, [
                'logo_path'   => null,
                'status'      => 1,
                'display_mode' => 'products_and_description',
                'additional'  => null,
                'banner_path' => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]));
        }

        // Category translations (all supported locales)
        foreach ($locales as $locale) {
            $rows = [];

            $translations = [
                2 => ['name' => 'Fruits & Vegetables', 'slug' => 'fruits-vegetables'],
                3 => ['name' => 'Meat & Seafood',      'slug' => 'meat-seafood'],
                4 => ['name' => 'Bread & Bakery',       'slug' => 'bread-bakery'],
                5 => ['name' => 'Drink',                'slug' => 'drink'],
                6 => ['name' => 'Spices & Herbs',       'slug' => 'spices-herbs'],
                7 => ['name' => 'Healthy Snacks',       'slug' => 'healthy-snacks'],
                8 => ['name' => 'Kitchen Essentials',   'slug' => 'kitchen-essentials'],
            ];

            foreach ($translations as $id => $t) {
                $slug = $locale === 'id'
                    ? ($t['slug'])
                    : ($t['slug']);

                $rows[] = [
                    'category_id'     => $id,
                    'name'            => $t['name'],
                    'slug'            => $slug,
                    'url_path'        => $slug,
                    'description'     => "<p>{$t['name']} - Kitchen essentials for your home</p>",
                    'meta_title'      => "{$t['name']} - Kitchen Needs",
                    'meta_description'=> "Shop {$t['name']} online at Ankesh Mart",
                    'meta_keywords'   => strtolower($t['name']),
                    'locale_id'       => null,
                    'locale'          => $locale,
                ];
            }

            DB::table('category_translations')->insert($rows);
        }

        // Filterable attributes for all 7 categories
        $filterableRows = [];
        for ($i = 2; $i <= 8; $i++) {
            $filterableRows[] = ['category_id' => $i, 'attribute_id' => 11];
            $filterableRows[] = ['category_id' => $i, 'attribute_id' => 23];
            $filterableRows[] = ['category_id' => $i, 'attribute_id' => 24];
            $filterableRows[] = ['category_id' => $i, 'attribute_id' => 25];
        }

        DB::table('category_filterable_attributes')->insert($filterableRows);
    }

    /**
     * Store image in storage.
     *
     * @param  string  $targetPath
     * @param  string  $file
     * @return string|null
     */
    public function storeFileIfExists($targetPath, $file)
    {
        if (file_exists(base_path(self::BASE_PATH.$file))) {
            return Storage::putFile($targetPath, new File(base_path(self::BASE_PATH.$file)));
        }

        return null;
    }
}
