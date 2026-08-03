<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Deploy Secret Key
    |--------------------------------------------------------------------------
    |
    | The secret key required to access deploy helper routes.
    | Generate one: php -r "echo bin2hex(random_bytes(32)).PHP_EOL;"
    |
    */

    'secret' => env('DEPLOY_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Allowed IPs (optional)
    |--------------------------------------------------------------------------
    |
    | Leave empty to allow all IPs. Add IPs to restrict access.
    | Example: ['127.0.0.1', '::1', '203.0.113.50']
    |
    */

    'allowed_ips' => array_filter(explode(',', env('DEPLOY_ALLOWED_IPS', ''))),

];
