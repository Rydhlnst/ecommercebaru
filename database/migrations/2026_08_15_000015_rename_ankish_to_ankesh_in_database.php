<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Helper to safely replace text in a column if table & column exist.
     */
    protected function replaceInTable(string $table, string $column): void
    {
        if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
            DB::table($table)
                ->where($column, 'like', '%ankish%')
                ->orWhere($column, 'like', '%Ankish%')
                ->update([
                    $column => DB::raw("REPLACE(REPLACE(`{$column}`, 'Ankish', 'Ankesh'), 'ankish', 'ankesh')"),
                ]);
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Admins table (Bagisto Admin user)
        $this->replaceInTable('admins', 'name');
        $this->replaceInTable('admins', 'email');

        // 2. Users table (Custom Admin / App users)
        $this->replaceInTable('users', 'name');
        $this->replaceInTable('users', 'email');

        // 3. Customers table
        $this->replaceInTable('customers', 'first_name');
        $this->replaceInTable('customers', 'last_name');
        $this->replaceInTable('customers', 'email');

        // 4. Channel translations table
        $this->replaceInTable('channel_translations', 'name');
        $this->replaceInTable('channel_translations', 'description');
        $this->replaceInTable('channel_translations', 'home_seo');
        $this->replaceInTable('channel_translations', 'home_page_content');
        $this->replaceInTable('channel_translations', 'footer_content');

        // 5. Channels table
        $this->replaceInTable('channels', 'home_seo');
        $this->replaceInTable('channels', 'hostname');

        // 6. Site settings table
        $this->replaceInTable('site_settings', 'value');

        // 7. Core config table
        $this->replaceInTable('core_config', 'value');

        // 8. CMS Pages & Categories if applicable
        $this->replaceInTable('cms_page_translations', 'page_title');
        $this->replaceInTable('cms_page_translations', 'html_content');
        $this->replaceInTable('category_translations', 'name');
        $this->replaceInTable('admin_categories', 'name');
        $this->replaceInTable('admin_products', 'name');
        $this->replaceInTable('blog_posts', 'title');
        $this->replaceInTable('blog_posts', 'content');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation
    }
};
