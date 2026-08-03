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
                    'description' => '<p>Fresh fruits and vegetables sourced from local farms</p>',
                    'meta-description' => 'Shop fresh fruits and vegetables online',
                    'meta-keywords' => 'fruits, vegetables, fresh produce, organic',
                    'meta-title' => 'Fruits & Vegetables - Fresh Produce',
                    'name' => 'Fruits & Vegetables',
                    'slug' => 'fruits-vegetables',
                    'url-path' => 'fruits-vegetables',
                ],

                '3' => [
                    'description' => '<p>Premium meat and seafood for your kitchen</p>',
                    'meta-description' => 'Shop fresh meat and seafood online',
                    'meta-keywords' => 'meat, seafood, chicken, beef, fish',
                    'meta-title' => 'Meat & Seafood - Fresh & Premium',
                    'name' => 'Meat & Seafood',
                    'slug' => 'meat-seafood',
                    'url-path' => 'meat-seafood',
                ],

                '4' => [
                    'description' => '<p>Freshly baked bread and bakery items</p>',
                    'meta-description' => 'Shop bread and bakery items online',
                    'meta-keywords' => 'bread, bakery, pastries, cakes',
                    'meta-title' => 'Bread & Bakery - Freshly Baked',
                    'name' => 'Bread & Bakery',
                    'slug' => 'bread-bakery',
                    'url-path' => 'bread-bakery',
                ],

                '5' => [
                    'description' => '<p>Refreshing drinks and beverages for every occasion</p>',
                    'meta-description' => 'Shop drinks and beverages online',
                    'meta-keywords' => 'drinks, beverages, juice, coffee, tea',
                    'meta-title' => 'Drink - Refreshing Beverages',
                    'name' => 'Drink',
                    'slug' => 'drink',
                    'url-path' => 'drink',
                ],

                '6' => [
                    'description' => '<p>Authentic spices and fresh herbs for flavorful cooking</p>',
                    'meta-description' => 'Shop spices and herbs online',
                    'meta-keywords' => 'spices, herbs, seasoning, cooking',
                    'meta-title' => 'Spices & Herbs - Authentic Flavors',
                    'name' => 'Spices & Herbs',
                    'slug' => 'spices-herbs',
                    'url-path' => 'spices-herbs',
                ],

                '7' => [
                    'description' => '<p>Healthy snacks for guilt-free munching</p>',
                    'meta-description' => 'Shop healthy snacks online',
                    'meta-keywords' => 'healthy snacks, nuts, dried fruits, granola',
                    'meta-title' => 'Healthy Snacks - Guilt-Free Munching',
                    'name' => 'Healthy Snacks',
                    'slug' => 'healthy-snacks',
                    'url-path' => 'healthy-snacks',
                ],

                '8' => [
                    'description' => '<p>Essential kitchen tools and accessories for every home</p>',
                    'meta-description' => 'Shop kitchen essentials online',
                    'meta-keywords' => 'kitchen essentials, cookware, utensils, tools',
                    'meta-title' => 'Kitchen Essentials - Tools for Every Home',
                    'name' => 'Kitchen Essentials',
                    'slug' => 'kitchen-essentials',
                    'url-path' => 'kitchen-essentials',
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
