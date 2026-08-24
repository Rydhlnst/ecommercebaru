<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Support\PolicyPages;
use Illuminate\Database\Seeder;

class PolicyPageSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PolicyPages::defaults() as $key => $content) {
            SiteSetting::firstOrCreate([
                'key' => $key,
            ], [
                'value' => $content,
            ]);
        }

        PolicyPages::sync(SiteSetting::getMany(PolicyPages::settingKeys()));
    }
}
