<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

class DeployController extends Controller
{
    /**
     * Secret key from .env — must match DEPLOY_SECRET.
     */
    private function secret(): string
    {
        return config('deploy.secret', '');
    }

    /**
     * Validate the request: correct secret + (optional) IP whitelist.
     */
    private function authorize(Request $request): ?string
    {
        $key = $request->query('key') ?? $request->input('key');

        if (! $key || ! hash_equals($this->secret(), $key)) {
            return 'Invalid or missing secret key.';
        }

        $allowedIps = config('deploy.allowed_ips', []);
        if (! empty($allowedIps) && ! in_array($request->ip(), $allowedIps, true)) {
            return 'IP '.$request->ip().' is not allowed.';
        }

        return null; // authorized
    }

    /**
     * Run artisan via Process (safe for cPanel — no SSH needed).
     */
    private function runCommand(string $command): array
    {
        $fullCommand = 'cd '.base_path().' && php artisan '.$command.' 2>&1';

        $result = Process::timeout(300)->run($fullCommand);

        return [
            'exit_code' => $result->exitCode,
            'stdout'    => $result->output(),
            'stderr'    => $result->errorOutput(),
        ];
    }

    /**
     * JSON response helper.
     */
    private function json(Response $response, int $status, mixed $data): Response
    {
        return $response->json($data, $status, [], JSON_PRETTY_PRINT);
    }

    // ─── ENDPOINTS ──────────────────────────────────────────────

    /**
     * GET /deploy?key=xxx&action=status
     * Quick health check.
     */
    public function index(Request $request)
    {
        if ($error = $this->authorize($request)) {
            return $this->json(response(), 403, ['error' => $error]);
        }

        return $this->json(response(), 200, [
            'status'  => 'ok',
            'app'     => config('app.name'),
            'env'     => config('app.env'),
            'url'     => config('app.url'),
            'time'    => now()->toDateTimeString(),
        ]);
    }

    /**
     * GET /deploy?key=xxx&action=seed-all
     * Full fresh seed: migrate:fresh + all seeders + index rebuild.
     */
    public function seedAll(Request $request)
    {
        if ($error = $this->authorize($request)) {
            return $this->json(response(), 403, ['error' => $error]);
        }

        $results = [];

        // 1. Migrate fresh
        $results['migrate'] = $this->runCommand('migrate:fresh --force');

        // 2. Seed attributes
        $results['seed_attributes'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Attribute\DatabaseSeeder" --force');

        // 3. Seed categories
        $results['seed_categories'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Category\CategoryTableSeeder" --force');

        // 4. Seed core (locales, currencies, countries, states, config)
        $results['seed_locales'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Core\LocalesTableSeeder" --force');
        $results['seed_currencies'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Core\CurrencyTableSeeder" --force');
        $results['seed_countries'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Core\CountriesTableSeeder" --force');
        $results['seed_states'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Core\StatesTableSeeder" --force');
        $results['seed_config'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Core\ConfigTableSeeder" --force');

        // 5. Seed customer groups
        $results['seed_customer_groups'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Customer\CustomerGroupTableSeeder" --force');

        // 6. Seed inventory
        $results['seed_inventory'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Inventory\InventorySourceTableSeeder" --force');

        // 7. Seed channel
        $results['seed_channel'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Core\ChannelTableSeeder" --force');

        // 8. Seed roles + admin
        $results['seed_roles'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\User\RolesTableSeeder" --force');
        $results['seed_admins'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\User\AdminsTableSeeder" --force');

        // 9. Seed theme
        $results['seed_theme'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Shop\ThemeCustomizationTableSeeder" --force');

        // 10. Seed products
        $results['seed_products'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\ProductTableSeeder" --force');

        // 11. Rebuild search index
        $results['index'] = $this->runCommand('indexer:index --mode=full');

        // Check for failures
        $failed = [];
        foreach ($results as $step => $r) {
            if ($r['exit_code'] !== 0) {
                $failed[$step] = $r;
            }
        }

        return $this->json(response(), empty($failed) ? 200 : 500, [
            'status'  => empty($failed) ? 'ok' : 'partial_failure',
            'steps'   => $results,
            'failed'  => $failed,
        ]);
    }

    /**
     * GET /deploy?key=xxx&action=seed-categories
     * Seed only categories + filterable attributes.
     */
    public function seedCategories(Request $request)
    {
        if ($error = $this->authorize($request)) {
            return $this->json(response(), 403, ['error' => $error]);
        }

        $results = [];
        $results['seed_categories'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Category\CategoryTableSeeder" --force');

        return $this->json(response(), 200, [
            'status' => 'ok',
            'steps'  => $results,
        ]);
    }

    /**
     * GET /deploy?key=xxx&action=seed-products
     * Seed only products.
     */
    public function seedProducts(Request $request)
    {
        if ($error = $this->authorize($request)) {
            return $this->json(response(), 403, ['error' => $error]);
        }

        $results = [];
        $results['seed_products'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\ProductTableSeeder" --force');

        return $this->json(response(), 200, [
            'status' => 'ok',
            'steps'  => $results,
        ]);
    }

    /**
     * GET /deploy?key=xxx&action=seed-theme
     * Seed homepage theme customizations.
     */
    public function seedTheme(Request $request)
    {
        if ($error = $this->authorize($request)) {
            return $this->json(response(), 403, ['error' => $error]);
        }

        $results = [];
        $results['seed_theme'] = $this->runCommand('db:seed --class="Webkul\Installer\Database\Seeders\Shop\ThemeCustomizationTableSeeder" --force');

        return $this->json(response(), 200, [
            'status' => 'ok',
            'steps'  => $results,
        ]);
    }

    /**
     * GET /deploy?key=xxx&action=cache-clear
     * Clear all caches.
     */
    public function cacheClear(Request $request)
    {
        if ($error = $this->authorize($request)) {
            return $this->json(response(), 403, ['error' => $error]);
        }

        $results = [];
        $results['view_clear']    = $this->runCommand('view:clear');
        $results['config_clear']  = $this->runCommand('config:clear');
        $results['route_clear']   = $this->runCommand('route:clear');
        $results['cache_clear']   = $this->runCommand('cache:clear');
        $results['response_clear']= $this->runCommand('responsecache:clear');
        $results['config_cache']  = $this->runCommand('config:cache');
        $results['route_cache']   = $this->runCommand('route:cache');
        $results['view_cache']    = $this->runCommand('view:cache');

        return $this->json(response(), 200, [
            'status' => 'ok',
            'steps'  => $results,
        ]);
    }

    /**
     * GET /deploy?key=xxx&action=index-rebuild
     * Rebuild the search index.
     */
    public function indexRebuild(Request $request)
    {
        if ($error = $this->authorize($request)) {
            return $this->json(response(), 403, ['error' => $error]);
        }

        $results = [];
        $results['index'] = $this->runCommand('indexer:index --mode=full');

        return $this->json(response(), 200, [
            'status' => 'ok',
            'steps'  => $results,
        ]);
    }

    /**
     * GET /deploy?key=xxx&action=storage-link
     * Create storage symlink (needed after deploy on cPanel).
     */
    public function storageLink(Request $request)
    {
        if ($error = $this->authorize($request)) {
            return $this->json(response(), 403, ['error' => $error]);
        }

        $results = [];
        $results['storage_link'] = $this->runCommand('storage:link --force');

        return $this->json(response(), 200, [
            'status' => 'ok',
            'steps'  => $results,
        ]);
    }

    /**
     * GET /deploy?key=xxx&action=chmod
     * Fix storage permissions (cPanel).
     */
    public function fixPermissions(Request $request)
    {
        if ($error = $this->authorize($request)) {
            return $this->json(response(), 403, ['error' => $error]);
        }

        $dirs = [
            storage_path(),
            bootstrap_path().'/cache',
        ];

        $results = [];
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                @chmod($dir, 0775);
                @chgrp($dir, getmygid());
                $results[$dir] = 'chmod 775';
            }
        }

        return $this->json(response(), 200, [
            'status' => 'ok',
            'steps'  => $results,
        ]);
    }

    /**
     * GET /deploy?key=xxx&action=run&cmd=COMMAND
     * Run any artisan command (dangerous — use with caution).
     * cmd param is validated against a whitelist.
     */
    public function run(Request $request)
    {
        if ($error = $this->authorize($request)) {
            return $this->json(response(), 403, ['error' => $error]);
        }

        $cmd = $request->query('cmd', '');

        // Whitelist of allowed commands
        $allowed = [
            'view:clear', 'view:cache',
            'config:clear', 'config:cache',
            'route:clear', 'route:cache',
            'cache:clear', 'cache:forget',
            'responsecache:clear',
            'storage:link',
            'key:generate',
            'migrate',
            'migrate:fresh',
            'db:seed',
            'indexer:index',
            'queue:restart',
        ];

        $cmdBase = trim(explode(' ', $cmd)[0]);

        if (! in_array($cmdBase, $allowed, true)) {
            return $this->json(response(), 403, [
                'error'   => 'Command "'.$cmdBase.'" is not whitelisted.',
                'allowed' => $allowed,
            ]);
        }

        // Force flag for dangerous commands
        if (in_array($cmdBase, ['migrate:fresh', 'db:seed', 'migrate']) && ! str_contains($cmd, '--force')) {
            $cmd .= ' --force';
        }

        $result = $this->runCommand($cmd);

        return $this->json(response(), $result['exit_code'] === 0 ? 200 : 500, [
            'status'   => $result['exit_code'] === 0 ? 'ok' : 'error',
            'command'  => $cmd,
            'exit_code'=> $result['exit_code'],
            'output'   => $result['stdout'],
            'errors'   => $result['stderr'],
        ]);
    }
}
