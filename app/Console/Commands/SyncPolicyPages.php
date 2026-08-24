<?php

namespace App\Console\Commands;

use Database\Seeders\PolicyPageSeeder;
use Illuminate\Console\Command;

class SyncPolicyPages extends Command
{
    protected $signature = 'policy:sync';

    protected $description = 'Create missing policy settings and publish their current content to storefront CMS pages';

    public function handle(): int
    {
        $this->components->info('Syncing policy settings and CMS pages...');

        app(PolicyPageSeeder::class)->run();

        $this->components->info('Policy settings and CMS pages are synced.');

        return self::SUCCESS;
    }
}
