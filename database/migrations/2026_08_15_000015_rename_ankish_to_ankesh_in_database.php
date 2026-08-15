<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update admins table (Bagisto Admin user)
        if (Schema::hasTable('admins')) {
            DB::table('admins')
                ->where('name', 'like', '%ankish%')
                ->orWhere('name', 'like', '%Ankish%')
                ->update([
                    'name' => DB::raw("REPLACE(REPLACE(name, 'Ankish', 'Ankesh'), 'ankish', 'ankesh')"),
                ]);

            DB::table('admins')
                ->where('email', 'like', '%ankish%')
                ->update([
                    'email' => DB::raw("REPLACE(email, 'ankish', 'ankesh')"),
                ]);
        }

        // 2. Update users table (Custom Admin / App users)
        if (Schema::hasTable('users')) {
            DB::table('users')
                ->where('name', 'like', '%ankish%')
                ->orWhere('name', 'like', '%Ankish%')
                ->update([
                    'name' => DB::raw("REPLACE(REPLACE(name, 'Ankish', 'Ankesh'), 'ankish', 'ankesh')"),
                ]);

            DB::table('users')
                ->where('email', 'like', '%ankish%')
                ->update([
                    'email' => DB::raw("REPLACE(email, 'ankish', 'ankesh')"),
                ]);
        }

        // 3. Update customers table
        if (Schema::hasTable('customers')) {
            DB::table('customers')
                ->where('first_name', 'like', '%ankish%')
                ->orWhere('first_name', 'like', '%Ankish%')
                ->update([
                    'first_name' => DB::raw("REPLACE(REPLACE(first_name, 'Ankish', 'Ankesh'), 'ankish', 'ankesh')"),
                ]);

            DB::table('customers')
                ->where('last_name', 'like', '%ankish%')
                ->orWhere('last_name', 'like', '%Ankish%')
                ->update([
                    'last_name' => DB::raw("REPLACE(REPLACE(last_name, 'Ankish', 'Ankesh'), 'ankish', 'ankesh')"),
                ]);

            DB::table('customers')
                ->where('email', 'like', '%ankish%')
                ->update([
                    'email' => DB::raw("REPLACE(email, 'ankish', 'ankesh')"),
                ]);
        }

        // 4. Update channels & channel_translations
        if (Schema::hasTable('channels')) {
            DB::table('channels')
                ->where('name', 'like', '%ankish%')
                ->orWhere('name', 'like', '%Ankish%')
                ->update([
                    'name' => DB::raw("REPLACE(REPLACE(name, 'Ankish', 'Ankesh'), 'ankish', 'ankesh')"),
                ]);
        }

        if (Schema::hasTable('channel_translations')) {
            DB::table('channel_translations')
                ->where('home_seo', 'like', '%ankish%')
                ->orWhere('home_seo', 'like', '%Ankish%')
                ->update([
                    'home_seo' => DB::raw("REPLACE(REPLACE(home_seo, 'Ankish', 'Ankesh'), 'ankish', 'ankesh')"),
                ]);
        }

        // 5. Update site_settings table
        if (Schema::hasTable('site_settings')) {
            DB::table('site_settings')
                ->where('value', 'like', '%ankish%')
                ->orWhere('value', 'like', '%Ankish%')
                ->update([
                    'value' => DB::raw("REPLACE(REPLACE(value, 'Ankish', 'Ankesh'), 'ankish', 'ankesh')"),
                ]);
        }

        // 6. Update core_config table
        if (Schema::hasTable('core_config')) {
            DB::table('core_config')
                ->where('value', 'like', '%ankish%')
                ->orWhere('value', 'like', '%Ankish%')
                ->update([
                    'value' => DB::raw("REPLACE(REPLACE(value, 'Ankish', 'Ankesh'), 'ankish', 'ankesh')"),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation
    }
};
