<?php

return [
    'seeders' => [
        'attribute' => [
            'attribute-families' => [
                'default' => 'Default',
            ],

            'attribute-groups' => [
                'description' => 'Description',
                'general' => 'General',
                'inventories' => 'Inventories',
                'meta-description' => 'Meta Description',
                'price' => 'Price',
                'rma' => 'RMA',
                'settings' => 'Settings',
                'shipping' => 'Shipping',
            ],

            'attributes' => [
                'allow-rma' => 'Allow RMA',
                'brand' => 'Brand',
                'color' => 'Color',
                'cost' => 'Cost',
                'description' => 'Description',
                'featured' => 'Featured',
                'guest-checkout' => 'Guest Checkout',
                'height' => 'Height',
                'length' => 'Length',
                'manage-stock' => 'Manage Stock',
                'meta-description' => 'Meta Description',
                'meta-keywords' => 'Meta Keywords',
                'meta-title' => 'Meta Title',
                'name' => 'Name',
                'new' => 'New',
                'price' => 'Price',
                'product-number' => 'Product Number',
                'rma-rules' => 'RMA Rules',
                'short-description' => 'Short Description',
                'size' => 'Size',
                'sku' => 'SKU',
                'special-price' => 'Special Price',
                'special-price-from' => 'Special Price From',
                'special-price-to' => 'Special Price To',
                'status' => 'Status',
                'tax-category' => 'Tax Category',
                'url-key' => 'URL Key',
                'visible-individually' => 'Visible Individually',
                'weight' => 'Weight',
                'width' => 'Width',
            ],

            'attribute-options' => [
                'black' => 'Black',
                'green' => 'Green',
                'l' => 'L',
                'm' => 'M',
                'red' => 'Red',
                's' => 'S',
                'white' => 'White',
                'xl' => 'XL',
                'yellow' => 'Yellow',
            ],
        ],

        'category' => [
            'categories' => [
                'description' => 'Root Category Description',
                'name' => 'Root',
            ],
        ],

        'cms' => [
            'pages' => [
                'about-us' => [
                    'content' => 'About Us Page Content',
                    'title' => 'About Us',
                ],

                'contact-us' => [
                    'content' => 'Contact Us Page Content',
                    'title' => 'Contact Us',
                ],

                'customer-service' => [
                    'content' => 'Customer Service Page Content',
                    'title' => 'Customer Service',
                ],

                'payment-policy' => [
                    'content' => 'Payment Policy Page Content',
                    'title' => 'Payment Policy',
                ],

                'privacy-policy' => [
                    'content' => 'Privacy Policy Page Content',
                    'title' => 'Privacy Policy',
                ],

                'refund-policy' => [
                    'content' => 'Refund Policy Page Content',
                    'title' => 'Refund Policy',
                ],

                'return-policy' => [
                    'content' => 'Return Policy Page Content',
                    'title' => 'Return Policy',
                ],

                'shipping-policy' => [
                    'content' => 'Shipping Policy Page Content',
                    'title' => 'Shipping Policy',
                ],

                'terms-conditions' => [
                    'content' => 'Terms & Conditions Page Content',
                    'title' => 'Terms & Conditions',
                ],

                'terms-of-use' => [
                    'content' => 'Terms of Use Page Content',
                    'title' => 'Terms of Use',
                ],

                'whats-new' => [
                    'content' => 'What\'s New page content',
                    'title' => 'What\'s New',
                ],
            ],
        ],

        'core' => [
            'channels' => [
                'meta-description' => 'Demo store meta description',
                'meta-keywords' => 'Demo store meta keyword',
                'meta-title' => 'Demo store',
                'name' => 'Default',
            ],

            'currencies' => [
                'AED' => 'United Arab Emirates Dirham',
                'ARS' => 'Argentine Peso',
                'AUD' => 'Australian Dollar',
                'BDT' => 'Bangladeshi Taka',
                'BHD' => 'Bahraini Dinar',
                'BRL' => 'Brazilian Real',
                'CAD' => 'Canadian Dollar',
                'CHF' => 'Swiss Franc',
                'CLP' => 'Chilean Peso',
                'CNY' => 'Chinese Yuan',
                'COP' => 'Colombian Peso',
                'CZK' => 'Czech Koruna',
                'DKK' => 'Danish Krone',
                'DZD' => 'Algerian Dinar',
                'EGP' => 'Egyptian Pound',
                'EUR' => 'Euro',
                'FJD' => 'Fijian Dollar',
                'GBP' => 'British Pound Sterling',
                'HKD' => 'Hong Kong Dollar',
                'HUF' => 'Hungarian Forint',
                'IDR' => 'Indonesian Rupiah',
                'ILS' => 'Israeli New Shekel',
                'INR' => 'Indian Rupee',
                'JOD' => 'Jordanian Dinar',
                'JPY' => 'Japanese Yen',
                'KRW' => 'South Korean Won',
                'KWD' => 'Kuwaiti Dinar',
                'KZT' => 'Kazakhstani Tenge',
                'LBP' => 'Lebanese Pound',
                'LKR' => 'Sri Lankan Rupee',
                'LYD' => 'Libyan Dinar',
                'MAD' => 'Moroccan Dirham',
                'MUR' => 'Mauritian Rupee',
                'MXN' => 'Mexican Peso',
                'MYR' => 'Malaysian Ringgit',
                'NGN' => 'Nigerian Naira',
                'NOK' => 'Norwegian Krone',
                'NPR' => 'Nepalese Rupee',
                'NZD' => 'New Zealand Dollar',
                'OMR' => 'Omani Rial',
                'PAB' => 'Panamanian Balboa',
                'PEN' => 'Peruvian Nuevo Sol',
                'PHP' => 'Philippine Peso',
                'PKR' => 'Pakistani Rupee',
                'PLN' => 'Polish Zloty',
                'PYG' => 'Paraguayan Guarani',
                'QAR' => 'Qatari Rial',
                'RON' => 'Romanian Leu',
                'RUB' => 'Russian Ruble',
                'SAR' => 'Saudi Riyal',
                'SEK' => 'Swedish Krona',
                'SGD' => 'Singapore Dollar',
                'THB' => 'Thai Baht',
                'TND' => 'Tunisian Dinar',
                'TRY' => 'Turkish Lira',
                'TWD' => 'New Taiwan Dollar',
                'UAH' => 'Ukrainian Hryvnia',
                'USD' => 'United States Dollar',
                'UZS' => 'Uzbekistani Som',
                'VES' => 'Venezuelan Bolívar',
                'VND' => 'Vietnamese Dong',
                'XAF' => 'CFA Franc BEAC',
                'XOF' => 'CFA Franc BCEAO',
                'ZAR' => 'South African Rand',
                'ZMW' => 'Zambian Kwacha',
            ],

            'locales' => [
                'ar' => 'Arabic',
                'bn' => 'Bengali',
                'ca' => 'Catalan',
                'de' => 'German',
                'en' => 'English',
                'es' => 'Spanish',
                'fa' => 'Persian',
                'fr' => 'French',
                'he' => 'Hebrew',
                'hi_IN' => 'Hindi',
                'id' => 'Indonesian',
                'it' => 'Italian',
                'ja' => 'Japanese',
                'nl' => 'Dutch',
                'pl' => 'Polish',
                'pt_BR' => 'Brazilian Portuguese',
                'ro' => 'Romanian',
                'ru' => 'Russian',
                'sin' => 'Sinhala',
                'tr' => 'Turkish',
                'uk' => 'Ukrainian',
                'zh_CN' => 'Chinese',
            ],
        ],

        'customer' => [
            'customer-groups' => [
                'general' => 'General',
                'guest' => 'Guest',
                'wholesale' => 'Wholesale',
            ],
        ],

        'inventory' => [
            'inventory-sources' => [
                'name' => 'Default',
            ],
        ],

        'shop' => [
            'theme-customizations' => [
                'bold-collections' => [
                    'content' => [
                        'btn-title' => 'View Collections',
                        'description' => 'Introducing Our New Bold Collections! Elevate your style with daring designs and vibrant statements. Explore striking patterns and bold colors that redefine your wardrobe. Get ready to embrace the extraordinary!',
                        'title' => 'Get Ready for our new Bold Collections!',
                    ],

                    'name' => 'Bold Collections',
                ],

                'bold-collections-2' => [
                    'content' => [
                        'btn-title' => 'View Collections',
                        'description' => 'Our Bold Collections are here to redefine your wardrobe with fearless designs and striking, vibrant colors. From daring patterns to powerful hues, this is your chance to break away from the ordinary and step into the extraordinary.',
                        'title' => 'Unleash Your Boldness with Our New Collection!',
                    ],

                    'name' => 'Bold Collections',
                ],

                'book-tickets' => [
                    'name' => 'Book Tickets',

                    'options' => [
                        'title' => 'Book Tickets',
                    ],
                ],

                'categories-collections' => [
                    'name' => 'Categories Collections',
                ],

                'footer-links' => [
                    'name' => 'Footer Links',

                    'options' => [
                        'about-us' => 'About Us',
                        'contact-us' => 'Contact Us',
                        'customer-service' => 'Customer Service',
                        'payment-policy' => 'Payment Policy',
                        'privacy-policy' => 'Privacy Policy',
                        'refund-policy' => 'Refund Policy',
                        'return-policy' => 'Return Policy',
                        'shipping-policy' => 'Shipping Policy',
                        'terms-conditions' => 'Terms & Conditions',
                        'terms-of-use' => 'Terms of Use',
                        'whats-new' => 'What\'s New',
                    ],
                ],

                'game-container' => [
                    'content' => [
                        'sub-title-1' => 'Our Collections',
                        'sub-title-2' => 'Our Collections',
                        'title' => 'The game with our new additions!',
                    ],

                    'name' => 'Game Container',
                ],

                'image-carousel' => [
                    'name' => 'Image Carousel',

                    'sliders' => [
                        'title' => 'Get Ready For New Collection',
                    ],
                ],

                'kids-collection' => [
                    'name' => 'Kids Collection',

                    'options' => [
                        'title' => 'Kids Collection',
                    ],
                ],

                'mens-collection' => [
                    'name' => 'Mens Collection',

                    'options' => [
                        'title' => 'Mens Collection',
                    ],
                ],

                'offer-information' => [
                    'content' => [
                        'title' => 'Get UPTO 40% OFF on your 1st order SHOP NOW',
                    ],

                    'name' => 'Offer Information',
                ],

                'services-content' => [
                    'description' => [
                        'emi-available-info' => 'No cost EMI available on all major credit cards',
                        'free-shipping-info' => 'Enjoy free shipping on all orders',
                        'product-replace-info' => 'Easy Product Replacement Available!',
                        'time-support-info' => 'Dedicated 24/7 support via chat and email',
                    ],

                    'name' => 'Services Content',

                    'title' => [
                        'emi-available' => 'Emi Available',
                        'free-shipping' => 'Free Shipping',
                        'product-replace' => 'Product Replace',
                        'time-support' => '24/7 Support',
                    ],
                ],

                'top-collections' => [
                    'content' => [
                        'sub-title-1' => 'Our Collections',
                        'sub-title-2' => 'Our Collections',
                        'sub-title-3' => 'Our Collections',
                        'sub-title-4' => 'Our Collections',
                        'sub-title-5' => 'Our Collections',
                        'sub-title-6' => 'Our Collections',
                        'title' => 'The game with our new additions!',
                    ],

                    'name' => 'Top Collections',
                ],

                'womens-collection' => [
                    'name' => 'Womens Collection',

                    'options' => [
                        'title' => 'Womens Collection',
                    ],
                ],
            ],
        ],

        'user' => [
            'roles' => [
                'description' => 'This role users will have all the access',
                'name' => 'Administrator',
            ],

            'users' => [
                'name' => 'Example',
            ],
        ],

        'sample-categories' => [
            'category-translation' => [
                '2' => [
                    'description' => '<p>Grannis Signature Kits - Premium spice collections curated for authentic flavors</p>',
                    'meta-description' => 'Discover our signature spice kits, handpicked for authentic cooking experiences',
                    'meta-keywords' => 'signature kits, spice collection, premium spices, cooking kits',
                    'meta-title' => 'Grannis Signature Kits - Premium Spice Collections',
                    'name' => 'Grannis Signature Kits',
                    'slug' => 'grannis-signature-kits',
                    'url-path' => 'grannis-signature-kits',
                ],

                '3' => [
                    'description' => '<p>Oats With Nuts - Healthy breakfast options with premium oats and nuts</p>',
                    'meta-description' => 'Healthy oats and nuts blends for nutritious breakfast and snacking',
                    'meta-keywords' => 'oats, nuts, healthy breakfast, organic oats, mixed nuts',
                    'meta-title' => 'Oats With Nuts - Healthy Breakfast Options',
                    'name' => 'Oats With Nuts',
                    'slug' => 'oats-with-nuts',
                    'url-path' => 'oats-with-nuts',
                ],

                '4' => [
                    'description' => '<p>Recipe Mix Masalas - Ready-to-use spice blends for authentic Indian dishes</p>',
                    'meta-description' => 'Authentic recipe mix masalas for perfect Indian cooking every time',
                    'meta-keywords' => 'recipe mix, masalas, spice blends, Indian cooking, curry mix',
                    'meta-title' => 'Recipe Mix Masalas - Authentic Spice Blends',
                    'name' => 'Recipe Mix Masalas',
                    'slug' => 'recipe-mix-masalas',
                    'url-path' => 'recipe-mix-masalas',
                ],

                '5' => [
                    'description' => '<p>Seeds And Super Foods - Nutrient-rich seeds and superfoods for healthy living</p>',
                    'meta-description' => 'Premium seeds and superfoods packed with nutrients and antioxidants',
                    'meta-keywords' => 'seeds, superfoods, chia seeds, flax seeds, healthy eating',
                    'meta-title' => 'Seeds And Super Foods - Nutrient-Rich Options',
                    'name' => 'Seeds And Super Foods',
                    'slug' => 'seeds-and-super-foods',
                    'url-path' => 'seeds-and-super-foods',
                ],

                '6' => [
                    'description' => '<p>Spices And Salts - Pure and authentic spices and specialty salts</p>',
                    'meta-description' => 'Pure, authentic spices and specialty salts for enhanced flavor',
                    'meta-keywords' => 'spices, salts, pure spices, himalayan salt, black salt',
                    'meta-title' => 'Spices And Salts - Pure & Authentic',
                    'name' => 'Spices And Salts',
                    'slug' => 'spices-and-salts',
                    'url-path' => 'spices-and-salts',
                ],

                '7' => [
                    'description' => '<p>Desi Ghee - Traditional clarified butter for authentic Indian cooking</p>',
                    'meta-description' => 'Premium quality desi ghee made from traditional methods',
                    'meta-keywords' => 'desi ghee, clarified butter, pure ghee, Indian ghee',
                    'meta-title' => 'Desi Ghee - Traditional Clarified Butter',
                    'name' => 'Desi Ghee',
                    'slug' => 'desi-ghee',
                    'url-path' => 'desi-ghee',
                ],

                '8' => [
                    'description' => '<p>Fry Mix Masala - Ready-to-use spice mixes for frying and sautéing</p>',
                    'meta-description' => 'Specialty fry mix masalas for perfect fried and sautéed dishes',
                    'meta-keywords' => 'fry mix, masala, frying spices, sauté mix, cooking spices',
                    'meta-title' => 'Fry Mix Masala - Perfect Frying Spices',
                    'name' => 'Fry Mix Masala',
                    'slug' => 'fry-mix-masala',
                    'url-path' => 'fry-mix-masala',
                ],

                '9' => [
                    'description' => '<p>Complete spice kits for diabetic-friendly cooking</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Diabetic Special Kits',
                    'slug' => 'diabetic-special-kits',
                    'url-path' => 'grannis-signature-kits/diabetic-special-kits',
                ],

                '10' => [
                    'description' => '<p>Everyday cooking spice collections</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Everyday Collections',
                    'slug' => 'everyday-collections',
                    'url-path' => 'grannis-signature-kits/everyday-collections',
                ],

                '11' => [
                    'description' => '<p>Premium gift sets for spice lovers</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Gift Sets',
                    'slug' => 'gift-sets',
                    'url-path' => 'grannis-signature-kits/gift-sets',
                ],

                '12' => [
                    'description' => '<p>Steel cut oats with premium nuts</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Steel Cut Oats',
                    'slug' => 'steel-cut-oats',
                    'url-path' => 'oats-with-nuts/steel-cut-oats',
                ],

                '13' => [
                    'description' => '<p>Flavored oat blends with nuts and dried fruits</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Flavored Blends',
                    'slug' => 'flavored-blends',
                    'url-path' => 'oats-with-nuts/flavored-blends',
                ],

                '14' => [
                    'description' => '<p>Curry powder blends for various dishes</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Curry Powders',
                    'slug' => 'curry-powders',
                    'url-path' => 'recipe-mix-masalas/curry-powders',
                ],

                '15' => [
                    'description' => '<p>Biryani and pulao spice mixes</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Biryani & Pulao Mix',
                    'slug' => 'biryani-pulao-mix',
                    'url-path' => 'recipe-mix-masalas/biryani-pulao-mix',
                ],

                '16' => [
                    'description' => '<p>Chia, flax, and pumpkin seeds</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Chia & Flax Seeds',
                    'slug' => 'chia-flax-seeds',
                    'url-path' => 'seeds-and-super-foods/chia-flax-seeds',
                ],

                '17' => [
                    'description' => '<p>Moringa, spirulina, and other superfood powders</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Superfood Powders',
                    'slug' => 'superfood-powders',
                    'url-path' => 'seeds-and-super-foods/superfood-powders',
                ],

                '18' => [
                    'description' => '<p>Pure turmeric, cumin, coriander, and other whole spices</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Whole Spices',
                    'slug' => 'whole-spices',
                    'url-path' => 'spices-and-salts/whole-spices',
                ],

                '19' => [
                    'description' => '<p>Himalayan pink salt, black salt, and specialty salts</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Specialty Salts',
                    'slug' => 'specialty-salts',
                    'url-path' => 'spices-and-salts/specialty-salts',
                ],

                '20' => [
                    'description' => '<p>Cow ghee and buffalo ghee</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Cow Ghee',
                    'slug' => 'cow-ghee',
                    'url-path' => 'desi-ghee/cow-ghee',
                ],

                '21' => [
                    'description' => '<p>Flavored ghee variants</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Flavored Ghee',
                    'slug' => 'flavored-ghee',
                    'url-path' => 'desi-ghee/flavored-ghee',
                ],

                '22' => [
                    'description' => '<p>Non-veg fry masala blends</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Non-Veg Fry Mix',
                    'slug' => 'non-veg-fry-mix',
                    'url-path' => 'fry-mix-masala/non-veg-fry-mix',
                ],

                '23' => [
                    'description' => '<p>Veg fry masala blends</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Veg Fry Mix',
                    'slug' => 'veg-fry-mix',
                    'url-path' => 'fry-mix-masala/veg-fry-mix',
                ],],

                '28' => [
                    'description' => '<p>Discover smartphones, chargers, cases, and other essentials for staying connected on the go.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Mobile Phones & Accessories',
                    'slug' => 'mobile-phones-accessories',
                    'url-path' => '',
                ],

                '29' => [
                    'description' => '<p>Find powerful laptops and portable tablets for work, study, and play.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Laptops & Tablets',
                    'slug' => 'laptops-tablets',
                    'url-path' => '',
                ],

                '30' => [
                    'description' => '<p>Shop headphones, earbuds, and speakers to enjoy crystal-clear sound and immersive audio experiences.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Audio Devices',
                    'slug' => 'audio-devices',
                    'url-path' => '',
                ],

                '31' => [
                    'description' => '<p>Make life easier with smart lighting, thermostats, security systems, and more.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Smart Home & Automation',
                    'slug' => 'smart-home-automation',
                    'url-path' => '',
                ],

                '32' => [
                    'description' => '<p>Upgrade your living space with functional and stylish home and kitchen essentials. From cooking to cleaning, find products that enhance comfort and efficiency.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Household',
                    'slug' => 'household',
                    'url-path' => '',
                ],

                '33' => [
                    'description' => '<p>Browse blenders, air fryers, coffee makers, and more to simplify meal prep.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Kitchen Appliances',
                    'slug' => 'kitchen-appliances',
                    'url-path' => '',
                ],

                '34' => [
                    'description' => '<p>Explore cookware sets, utensils, dinnerware, and serveware for your culinary needs.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Cookware & Dining',
                    'slug' => 'cookware-dining',
                    'url-path' => '',
                ],

                '35' => [
                    'description' => '<p>Add comfort and charm with sofas, tables, wall art, and home accents.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Furniture & Decor',
                    'slug' => 'furniture-decor',
                    'url-path' => '',
                ],

                '36' => [
                    'description' => '<p>Keep your space spotless with vacuums, cleaning sprays, brooms, and organizers.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Cleaning Supplies',
                    'slug' => 'cleaning-supplies',
                    'url-path' => '',
                ],

                '37' => [
                    'description' => '<p>Ignite your imagination or organize your workspace with a wide selection of books and stationery. Perfect for readers, students, professionals, and artists.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Books & Stationery',
                    'slug' => 'books-stationery',
                    'url-path' => '',
                ],

                '38' => [
                    'description' => '<p>Dive into bestselling novels, biographies, self-help, and more.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Fiction & Non-Fiction Books',
                    'slug' => 'fiction-non-fiction-books',
                    'url-path' => '',
                ],

                '39' => [
                    'description' => '<p>Find textbooks, reference materials, and study guides for all ages.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Educational & Academic',
                    'slug' => 'educational-academic',
                    'url-path' => '',
                ],

                '40' => [
                    'description' => '<p>Shop pens, notebooks, planners, and office essentials for productivity.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Office Supplies',
                    'slug' => 'office-supplies',
                    'url-path' => '',
                ],

                '41' => [
                    'description' => '<p>Explore paints, brushes, sketchbooks, and DIY craft kits for creatives.</p>',
                    'meta-description' => '',
                    'meta-keywords' => '',
                    'meta-title' => '',
                    'name' => 'Art & Craft Materials',
                    'slug' => 'art-craft-materials',
                    'url-path' => '',
                ],
            ],
        ],
    ],

    'installer' => [
        'middleware' => [
            'already-installed' => 'Application is already installed.',
        ],

        'index' => [
            'create-administrator' => [
                'admin' => 'Admin',
                'bagisto' => 'Bagisto',
                'confirm-password' => 'Confirm Password',
                'email' => 'Email',
                'email-address' => 'admin@example.com',
                'password' => 'Password',
                'title' => 'Create Administrator',
            ],

            'environment-configuration' => [
                'algerian-dinar' => 'Algerian Dinar (DZD)',
                'allowed-currencies' => 'Allowed Currencies',
                'allowed-locales' => 'Allowed Locales',
                'application-name' => 'Application Name',
                'argentine-peso' => 'Argentine Peso (ARS)',
                'australian-dollar' => 'Australian Dollar (AUD)',
                'bagisto' => 'Bagisto',
                'bangladeshi-taka' => 'Bangladeshi Taka (BDT)',
                'bahraini-dinar' => 'Bahraini Dinar (BHD)',
                'brazilian-real' => 'Brazilian Real (BRL)',
                'british-pound-sterling' => 'British Pound Sterling (GBP)',
                'canadian-dollar' => 'Canadian Dollar (CAD)',
                'cfa-franc-bceao' => 'CFA Franc BCEAO (XOF)',
                'cfa-franc-beac' => 'CFA Franc BEAC (XAF)',
                'chilean-peso' => 'Chilean Peso (CLP)',
                'chinese-yuan' => 'Chinese Yuan (CNY)',
                'colombian-peso' => 'Colombian Peso (COP)',
                'czech-koruna' => 'Czech Koruna (CZK)',
                'danish-krone' => 'Danish Krone (DKK)',
                'database-connection' => 'Database Connection',
                'database-hostname' => 'Database Hostname',
                'database-name' => 'Database Name',
                'database-password' => 'Database Password',
                'database-port' => 'Database Port',
                'database-prefix' => 'Database Prefix',
                'database-prefix-help' => 'The prefix should be 4 characters long and can only contain letters, numbers, and underscores.',
                'database-username' => 'Database Username',
                'default-currency' => 'Default Currency',
                'default-locale' => 'Default Locale',
                'default-timezone' => 'Default Timezone',
                'default-url' => 'Default URL',
                'default-url-link' => 'https://localhost',
                'egyptian-pound' => 'Egyptian Pound (EGP)',
                'euro' => 'Euro (EUR)',
                'fijian-dollar' => 'Fijian Dollar (FJD)',
                'hong-kong-dollar' => 'Hong Kong Dollar (HKD)',
                'hungarian-forint' => 'Hungarian Forint (HUF)',
                'indian-rupee' => 'Indian Rupee (INR)',
                'indonesian-rupiah' => 'Indonesian Rupiah (IDR)',
                'israeli-new-shekel' => 'Israeli New Shekel (ILS)',
                'japanese-yen' => 'Japanese Yen (JPY)',
                'jordanian-dinar' => 'Jordanian Dinar (JOD)',
                'kazakhstani-tenge' => 'Kazakhstani Tenge (KZT)',
                'kuwaiti-dinar' => 'Kuwaiti Dinar (KWD)',
                'lebanese-pound' => 'Lebanese Pound (LBP)',
                'libyan-dinar' => 'Libyan Dinar (LYD)',
                'malaysian-ringgit' => 'Malaysian Ringgit (MYR)',
                'mauritian-rupee' => 'Mauritian Rupee (MUR)',
                'mexican-peso' => 'Mexican Peso (MXN)',
                'moroccan-dirham' => 'Moroccan Dirham (MAD)',
                'mysql' => 'Mysql',
                'nepalese-rupee' => 'Nepalese Rupee (NPR)',
                'new-taiwan-dollar' => 'New Taiwan Dollar (TWD)',
                'new-zealand-dollar' => 'New Zealand Dollar (NZD)',
                'nigerian-naira' => 'Nigerian Naira (NGN)',
                'norwegian-krone' => 'Norwegian Krone (NOK)',
                'omani-rial' => 'Omani Rial (OMR)',
                'pakistani-rupee' => 'Pakistani Rupee (PKR)',
                'panamanian-balboa' => 'Panamanian Balboa (PAB)',
                'paraguayan-guarani' => 'Paraguayan Guarani (PYG)',
                'peruvian-nuevo-sol' => 'Peruvian Nuevo Sol (PEN)',
                'pgsql' => 'pgSQL',
                'philippine-peso' => 'Philippine Peso (PHP)',
                'polish-zloty' => 'Polish Zloty (PLN)',
                'qatari-rial' => 'Qatari Rial (QAR)',
                'romanian-leu' => 'Romanian Leu (RON)',
                'russian-ruble' => 'Russian Ruble (RUB)',
                'saudi-riyal' => 'Saudi Riyal (SAR)',
                'select-timezone' => 'Select Timezone',
                'singapore-dollar' => 'Singapore Dollar (SGD)',
                'south-african-rand' => 'South African Rand (ZAR)',
                'south-korean-won' => 'South Korean Won (KRW)',
                'sqlsrv' => 'SQLSRV',
                'sri-lankan-rupee' => 'Sri Lankan Rupee (LKR)',
                'swedish-krona' => 'Swedish Krona (SEK)',
                'swiss-franc' => 'Swiss Franc (CHF)',
                'thai-baht' => 'Thai Baht (THB)',
                'title' => 'Store Configuration',
                'tunisian-dinar' => 'Tunisian Dinar (TND)',
                'turkish-lira' => 'Turkish Lira (TRY)',
                'ukrainian-hryvnia' => 'Ukrainian Hryvnia (UAH)',
                'united-arab-emirates-dirham' => 'United Arab Emirates Dirham (AED)',
                'united-states-dollar' => 'United States Dollar (USD)',
                'uzbekistani-som' => 'Uzbekistani Som (UZS)',
                'venezuelan-bolívar' => 'Venezuelan Bolívar (VEF)',
                'vietnamese-dong' => 'Vietnamese Dong (VND)',
                'warning-message' => 'Beware! The settings for your default system language and default currency are permanent and cannot be changed once set.',
                'zambian-kwacha' => 'Zambian Kwacha (ZMW)',
            ],

            'sample-products' => [
                'no' => 'No',
                'note' => 'Note: Indexing time depends on the number of locales selected. This process may take up to 2 minutes to complete. If you add more locales, try to increase the max execution time in your server and PHP settings, or you can use our CLI installer to avoid request timeout.',
                'sample-products' => 'Sample Products',
                'title' => 'Sample Products',
                'yes' => 'Yes',
            ],

            'installation-processing' => [
                'bagisto' => 'Installation Bagisto',
                'bagisto-info' => 'Creating the database tables, this can take a few moments',
                'title' => 'Installation',
            ],

            'installation-completed' => [
                'admin-panel' => 'Admin Panel',
                'bagisto-forums' => 'Bagisto Forum',
                'customer-panel' => 'Customer Panel',
                'explore-bagisto-extensions' => 'Explore Bagisto Extension',
                'title' => 'Installation Completed',
                'title-info' => 'Bagisto is Successfully installed on your system.',
            ],

            'ready-for-installation' => [
                'create-database-tables' => 'Create the database tables',
                'drop-existing-tables' => 'Drop any existing tables present',
                'install' => 'Installation',
                'install-info' => 'Bagisto For Installation',
                'install-info-button' => 'Click the button below to',
                'populate-database-tables' => 'Populate the database tables',
                'start-installation' => 'Start Installation',
                'title' => 'Ready for Installation',
            ],

            'start' => [
                'language' => 'Installation Wizard language',
                'locale' => 'Locale',
                'main' => 'Start',
                'select-locale' => 'Select Locale',
                'title' => 'Your Bagisto install',
                'welcome-title' => 'Welcome to Bagisto',
            ],

            'server-requirements' => [
                'calendar' => 'Calendar',
                'ctype' => 'cType',
                'curl' => 'cURL',
                'dom' => 'dom',
                'fileinfo' => 'fileInfo',
                'filter' => 'Filter',
                'gd' => 'GD',
                'hash' => 'Hash',
                'intl' => 'intl',
                'json' => 'JSON',
                'mbstring' => 'mbstring',
                'openssl' => 'openssl',
                'pcre' => 'pcre',
                'pdo' => 'pdo',
                'php' => 'PHP',
                'php-version' => ':version or higher',
                'session' => 'session',
                'title' => 'System Requirements',
                'tokenizer' => 'tokenizer',
                'xml' => 'XML',
            ],

            'arabic' => 'Arabic',
            'back' => 'Back',
            'bagisto' => 'Bagisto',
            'bagisto-info' => 'a Community Project by',
            'bagisto-logo' => 'Bagisto Logo',
            'bengali' => 'Bengali',
            'catalan' => 'Catalan',
            'chinese' => 'Chinese',
            'continue' => 'Continue',
            'dutch' => 'Dutch',
            'english' => 'English',
            'french' => 'French',
            'german' => 'German',
            'hebrew' => 'Hebrew',
            'hindi' => 'Hindi',
            'indonesian' => 'Indonesian',
            'installation-description' => 'Bagisto installation typically involves several steps. Here\'s a general outline of the installation process for Bagisto',
            'installation-info' => 'We are happy to see you here!',
            'installation-title' => 'Welcome to Installation',
            'italian' => 'Italian',
            'japanese' => 'Japanese',
            'persian' => 'Persian',
            'polish' => 'Polish',
            'portuguese' => 'Brazilian Portuguese',
            'romanian' => 'Romanian',
            'russian' => 'Russian',
            'sinhala' => 'Sinhala',
            'spanish' => 'Spanish',
            'title' => 'Bagisto Installer',
            'turkish' => 'Turkish',
            'ukrainian' => 'Ukrainian',
            'webkul' => 'Webkul',
        ],
    ],
];
