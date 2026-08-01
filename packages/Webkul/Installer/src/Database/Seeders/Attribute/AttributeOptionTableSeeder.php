<?php

namespace Webkul\Installer\Database\Seeders\Attribute;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeOptionTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::table('attribute_options')->delete();

        DB::table('attribute_option_translations')->delete();

        $defaultLocale = $parameters['default_locale'] ?? config('app.locale');

        DB::table('attribute_options')->insert([
            [
                'id' => 1,
                'admin_name' => trans('installer::app.seeders.attribute.attribute-options.red', [], $defaultLocale),
                'sort_order' => 1,
                'attribute_id' => 23,
            ], [
                'id' => 2,
                'admin_name' => trans('installer::app.seeders.attribute.attribute-options.green', [], $defaultLocale),
                'sort_order' => 2,
                'attribute_id' => 23,
            ], [
                'id' => 3,
                'admin_name' => trans('installer::app.seeders.attribute.attribute-options.yellow', [], $defaultLocale),
                'sort_order' => 3,
                'attribute_id' => 23,
            ], [
                'id' => 4,
                'admin_name' => trans('installer::app.seeders.attribute.attribute-options.black', [], $defaultLocale),
                'sort_order' => 4,
                'attribute_id' => 23,
            ], [
                'id' => 5,
                'admin_name' => trans('installer::app.seeders.attribute.attribute-options.white', [], $defaultLocale),
                'sort_order' => 5,
                'attribute_id' => 23,
            ], [
                'id' => 6,
                'admin_name' => trans('installer::app.seeders.attribute.attribute-options.s', [], $defaultLocale),
                'sort_order' => 1,
                'attribute_id' => 24,
            ], [
                'id' => 7,
                'admin_name' => trans('installer::app.seeders.attribute.attribute-options.m', [], $defaultLocale),
                'sort_order' => 2,
                'attribute_id' => 24,
            ], [
                'id' => 8,
                'admin_name' => trans('installer::app.seeders.attribute.attribute-options.l', [], $defaultLocale),
                'sort_order' => 3,
                'attribute_id' => 24,
            ], [
                'id' => 9,
                'admin_name' => trans('installer::app.seeders.attribute.attribute-options.xl', [], $defaultLocale),
                'sort_order' => 4,
                'attribute_id' => 24,
            ],
            /**
             * Spice Level Options (attribute_id = 33)
             */
            [
                'id' => 10,
                'admin_name' => 'Mild',
                'sort_order' => 1,
                'attribute_id' => 33,
            ], [
                'id' => 11,
                'admin_name' => 'Medium',
                'sort_order' => 2,
                'attribute_id' => 33,
            ], [
                'id' => 12,
                'admin_name' => 'Hot',
                'sort_order' => 3,
                'attribute_id' => 33,
            ], [
                'id' => 13,
                'admin_name' => 'Extra Hot',
                'sort_order' => 4,
                'attribute_id' => 33,
            ],
            /**
             * Dietary Information Options (attribute_id = 36)
             */
            [
                'id' => 14,
                'admin_name' => 'Vegetarian',
                'sort_order' => 1,
                'attribute_id' => 36,
            ], [
                'id' => 15,
                'admin_name' => 'Vegan',
                'sort_order' => 2,
                'attribute_id' => 36,
            ], [
                'id' => 16,
                'admin_name' => 'Gluten-Free',
                'sort_order' => 3,
                'attribute_id' => 36,
            ], [
                'id' => 17,
                'admin_name' => 'Dairy-Free',
                'sort_order' => 4,
                'attribute_id' => 36,
            ], [
                'id' => 18,
                'admin_name' => 'Nut-Free',
                'sort_order' => 5,
                'attribute_id' => 36,
            ], [
                'id' => 19,
                'admin_name' => 'Halal',
                'sort_order' => 6,
                'attribute_id' => 36,
            ], [
                'id' => 20,
                'admin_name' => 'Kosher',
                'sort_order' => 7,
                'attribute_id' => 36,
            ],
            /**
             * Flavor Profile Options (attribute_id = 41)
             */
            [
                'id' => 21,
                'admin_name' => 'Earthy',
                'sort_order' => 1,
                'attribute_id' => 41,
            ], [
                'id' => 22,
                'admin_name' => 'Citrusy',
                'sort_order' => 2,
                'attribute_id' => 41,
            ], [
                'id' => 23,
                'admin_name' => 'Smoky',
                'sort_order' => 3,
                'attribute_id' => 41,
            ], [
                'id' => 24,
                'admin_name' => 'Sweet',
                'sort_order' => 4,
                'attribute_id' => 41,
            ], [
                'id' => 25,
                'admin_name' => 'Spicy',
                'sort_order' => 5,
                'attribute_id' => 41,
            ], [
                'id' => 26,
                'admin_name' => 'Savory',
                'sort_order' => 6,
                'attribute_id' => 41,
            ], [
                'id' => 27,
                'admin_name' => 'Bitter',
                'sort_order' => 7,
                'attribute_id' => 41,
            ], [
                'id' => 28,
                'admin_name' => 'Umami',
                'sort_order' => 8,
                'attribute_id' => 41,
            ],
            /**
             * Cuisine Type Options (attribute_id = 42)
             */
            [
                'id' => 29,
                'admin_name' => 'Indian',
                'sort_order' => 1,
                'attribute_id' => 42,
            ], [
                'id' => 30,
                'admin_name' => 'Thai',
                'sort_order' => 2,
                'attribute_id' => 42,
            ], [
                'id' => 31,
                'admin_name' => 'Mexican',
                'sort_order' => 3,
                'attribute_id' => 42,
            ], [
                'id' => 32,
                'admin_name' => 'Mediterranean',
                'sort_order' => 4,
                'attribute_id' => 42,
            ], [
                'id' => 33,
                'admin_name' => 'Chinese',
                'sort_order' => 5,
                'attribute_id' => 42,
            ], [
                'id' => 34,
                'admin_name' => 'Japanese',
                'sort_order' => 6,
                'attribute_id' => 42,
            ], [
                'id' => 35,
                'admin_name' => 'Korean',
                'sort_order' => 7,
                'attribute_id' => 42,
            ], [
                'id' => 36,
                'admin_name' => 'Middle Eastern',
                'sort_order' => 8,
                'attribute_id' => 42,
            ],
            /**
             * Net Weight / Size Options (attribute_id = 35)
             */
            [
                'id' => 37,
                'admin_name' => '200g',
                'sort_order' => 1,
                'attribute_id' => 35,
            ], [
                'id' => 38,
                'admin_name' => '250g',
                'sort_order' => 2,
                'attribute_id' => 35,
            ], [
                'id' => 39,
                'admin_name' => '500g',
                'sort_order' => 3,
                'attribute_id' => 35,
            ], [
                'id' => 40,
                'admin_name' => '500ml',
                'sort_order' => 4,
                'attribute_id' => 35,
            ], [
                'id' => 41,
                'admin_name' => '1000g',
                'sort_order' => 5,
                'attribute_id' => 35,
            ], [
                'id' => 42,
                'admin_name' => '1 KG',
                'sort_order' => 6,
                'attribute_id' => 35,
            ],
        ]);

        $locales = $parameters['allowed_locales'] ?? [$defaultLocale];

        foreach ($locales as $locale) {
            DB::table('attribute_option_translations')->insert([
                [
                    'locale' => $locale,
                    'label' => trans('installer::app.seeders.attribute.attribute-options.red', [], $locale),
                    'attribute_option_id' => 1,
                ], [
                    'locale' => $locale,
                    'label' => trans('installer::app.seeders.attribute.attribute-options.green', [], $locale),
                    'attribute_option_id' => 2,
                ], [
                    'locale' => $locale,
                    'label' => trans('installer::app.seeders.attribute.attribute-options.yellow', [], $locale),
                    'attribute_option_id' => 3,
                ], [
                    'locale' => $locale,
                    'label' => trans('installer::app.seeders.attribute.attribute-options.black', [], $locale),
                    'attribute_option_id' => 4,
                ], [
                    'locale' => $locale,
                    'label' => trans('installer::app.seeders.attribute.attribute-options.white', [], $locale),
                    'attribute_option_id' => 5,
                ], [
                    'locale' => $locale,
                    'label' => trans('installer::app.seeders.attribute.attribute-options.s', [], $locale),
                    'attribute_option_id' => 6,
                ], [
                    'locale' => $locale,
                    'label' => trans('installer::app.seeders.attribute.attribute-options.m', [], $locale),
                    'attribute_option_id' => 7,
                ], [
                    'locale' => $locale,
                    'label' => trans('installer::app.seeders.attribute.attribute-options.l', [], $locale),
                    'attribute_option_id' => 8,
                ], [
                    'locale' => $locale,
                    'label' => trans('installer::app.seeders.attribute.attribute-options.xl', [], $locale),
                    'attribute_option_id' => 9,
                ],
                // Net Weight translations
                [
                    'locale' => $locale,
                    'label' => '200g',
                    'attribute_option_id' => 37,
                ], [
                    'locale' => $locale,
                    'label' => '250g',
                    'attribute_option_id' => 38,
                ], [
                    'locale' => $locale,
                    'label' => '500g',
                    'attribute_option_id' => 39,
                ], [
                    'locale' => $locale,
                    'label' => '500ml',
                    'attribute_option_id' => 40,
                ], [
                    'locale' => $locale,
                    'label' => '1000g',
                    'attribute_option_id' => 41,
                ], [
                    'locale' => $locale,
                    'label' => '1 KG',
                    'attribute_option_id' => 42,
                ],
            ]);
        }
    }
}
