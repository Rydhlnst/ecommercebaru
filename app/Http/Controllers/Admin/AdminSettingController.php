<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Webkul\Core\Models\CoreConfig;

class AdminSettingController extends Controller
{
    /**
     * Mapping field form Pengaturan Toko → core config "Kontak & Lokasi".
     * Write-through: sekali simpan, semua tampilan storefront ikut berubah
     * (map section, footer, FAQ, tombol WhatsApp di halaman produk).
     */
    protected array $contactConfigMap = [
        'store_phone' => 'beres_storefront.contact.phone',
        'store_whatsapp' => 'beres_storefront.contact.whatsapp_number',
        'store_email' => 'beres_storefront.contact.email',
        'store_address' => 'beres_storefront.contact.address',
        'store_country' => 'beres_storefront.contact.country',
    ];

    /**
     * Mapping field form SEO → core config "SEO & Nama Tab Browser".
     */
    protected array $seoConfigMap = [
        'seo_site_name' => 'beres_storefront.seo.site_name',
        'seo_home_title' => 'beres_storefront.seo.home_title',
        'seo_title_suffix' => 'beres_storefront.seo.title_suffix',
    ];

    /**
     * Mapping section title form fields → core config keys.
     */
    protected array $sectionConfigMap = [
        'section_new_eyebrow' => 'beres_storefront.sections.new_eyebrow',
        'section_new_title' => 'beres_storefront.sections.new_title',
        'section_bundle_eyebrow' => 'beres_storefront.sections.bundle_eyebrow',
        'section_bundle_title' => 'beres_storefront.sections.bundle_title',
        'section_cat_eyebrow' => 'beres_storefront.sections.cat_eyebrow',
        'section_cat_title' => 'beres_storefront.sections.cat_title',
        'section_best_eyebrow' => 'beres_storefront.sections.best_eyebrow',
        'section_best_title' => 'beres_storefront.sections.best_title',
        'section_seed_eyebrow' => 'beres_storefront.sections.seed_eyebrow',
        'section_seed_title' => 'beres_storefront.sections.seed_title',
        'section_review_eyebrow' => 'beres_storefront.sections.review_eyebrow',
        'section_review_title' => 'beres_storefront.sections.review_title',
        'section_faq_title' => 'beres_storefront.sections.faq_title',
        'section_google_review_eyebrow' => 'beres_storefront.sections.google_review_eyebrow',
        'section_google_review_title' => 'beres_storefront.sections.google_review_title',
        'section_blog_eyebrow' => 'beres_storefront.sections.blog_eyebrow',
        'section_blog_title' => 'beres_storefront.sections.blog_title',
    ];

    /**
     * Simpan value ke core_config (sumber tunggal storefront).
     */
    protected function saveCoreConfig(string $code, ?string $value): void
    {
        try {
            CoreConfig::updateOrCreate(
                ['code' => $code, 'channel_code' => null, 'locale_code' => null],
                ['value' => $value ?? '']
            );
        } catch (QueryException $e) {
            // Lewati bila tabel core_config belum tersedia
        }
    }

    public function policy()
    {
        $policies = [];

        try {
            if (Schema::hasTable('site_settings')) {
                $policies = SiteSetting::getMany([
                    'policy_privacy',
                    'policy_refund',
                    'policy_shipping',
                    'policy_terms',
                ]);
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        return view('admin.setting.policy', compact('policies'));
    }

    public function updatePolicy(Request $request)
    {
        $validated = $request->validate([
            'policy_privacy' => 'nullable|string',
            'policy_refund' => 'nullable|string',
            'policy_shipping' => 'nullable|string',
            'policy_terms' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::setValue($key, $value);
        }

        return redirect()->route('admin.settings.policy')->with('success', 'Kebijakan berhasil diperbarui.');
    }

    public function store()
    {
        $settings = [];

        try {
            if (Schema::hasTable('site_settings')) {
                $settings = SiteSetting::getMany([
                    'store_whatsapp',
                    'store_maps_embed',
                    'store_country',
                    'store_address',
                    'store_phone',
                    'store_email',
                    'store_shopee',
                    'store_tokopedia',
                    'store_lazada',
                    'store_tiktok',
                    'store_instagram',
                    'store_facebook',
                    'store_youtube',
                    'header_nav_items',
                    'footer_newsletter_text',
                    'footer_col1_title',
                    'footer_col1_links',
                    'footer_col2_title',
                    'footer_col2_links',
                    'service_features',
                ]);
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        // Nilai SEO & nama tab dari core_config
        $settings = array_merge($settings, [
            'seo_site_name' => (string) core()->getConfigData('beres_storefront.seo.site_name'),
            'seo_home_title' => (string) core()->getConfigData('beres_storefront.seo.home_title'),
            'seo_title_suffix' => (string) core()->getConfigData('beres_storefront.seo.title_suffix'),
        ]);

        // Nilai section titles dari core_config
        foreach ($this->sectionConfigMap as $formKey => $configKey) {
            $settings[$formKey] = (string) core()->getConfigData($configKey);
        }

        return view('admin.setting.store', compact('settings'));
    }

    public function updateStore(Request $request)
    {
        if ($request->hasFile('hero_banner')) {
            $request->validate([
                'hero_banner' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            ]);
            $file = $request->file('hero_banner');
            $filename = 'hero-products.'.$file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            SiteSetting::setValue('hero_banner_image', '/images/'.$filename.'?v='.time());
        }

        $validated = $request->validate([
            'store_whatsapp' => 'nullable|string|max:20',
            'store_maps_embed' => 'nullable|string',
            'store_country' => 'nullable|string|max:100',
            'store_address' => 'nullable|string',
            'store_phone' => 'nullable|string|max:20',
            'store_email' => 'nullable|email|max:255',
            'store_shopee' => 'nullable|url|max:255',
            'store_tokopedia' => 'nullable|url|max:255',
            'store_lazada' => 'nullable|url|max:255',
            'store_tiktok' => 'nullable|url|max:255',
            'store_instagram' => 'nullable|url|max:255',
            'store_facebook' => 'nullable|url|max:255',
            'store_youtube' => 'nullable|url|max:255',
            'header_nav_items' => 'nullable|string',
            'footer_newsletter_text' => 'nullable|string',
            'footer_col1_title' => 'nullable|string|max:100',
            'footer_col2_title' => 'nullable|string|max:100',
            'seo_site_name' => 'nullable|string|max:255',
            'seo_home_title' => 'nullable|string|max:255',
            'seo_title_suffix' => 'nullable|string|max:255',
            'section_new_eyebrow' => 'nullable|string|max:255',
            'section_new_title' => 'nullable|string|max:255',
            'section_bundle_eyebrow' => 'nullable|string|max:255',
            'section_bundle_title' => 'nullable|string|max:255',
            'section_cat_eyebrow' => 'nullable|string|max:255',
            'section_cat_title' => 'nullable|string|max:255',
            'section_best_eyebrow' => 'nullable|string|max:255',
            'section_best_title' => 'nullable|string|max:255',
            'section_seed_eyebrow' => 'nullable|string|max:255',
            'section_seed_title' => 'nullable|string|max:255',
            'section_review_eyebrow' => 'nullable|string|max:255',
            'section_review_title' => 'nullable|string|max:255',
            'section_faq_title' => 'nullable|string|max:255',
            'section_google_review_eyebrow' => 'nullable|string|max:255',
            'section_google_review_title' => 'nullable|string|max:255',
            'section_blog_eyebrow' => 'nullable|string|max:255',
            'section_blog_title' => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::setValue($key, $value);
        }

        // Write-through: sinkronkan kontak & SEO ke core_config agar semua
        // tampilan storefront (map, footer, FAQ, title tab) ikut berubah.
        foreach ($this->contactConfigMap as $formKey => $configKey) {
            if (array_key_exists($formKey, $validated)) {
                $this->saveCoreConfig($configKey, $validated[$formKey]);
            }
        }

        foreach ($this->seoConfigMap as $formKey => $configKey) {
            if (array_key_exists($formKey, $validated)) {
                $this->saveCoreConfig($configKey, $validated[$formKey]);
            }
        }

        foreach ($this->sectionConfigMap as $formKey => $configKey) {
            if (array_key_exists($formKey, $validated)) {
                $this->saveCoreConfig($configKey, $validated[$formKey]);
            }
        }

        if ($request->has('feature_titles')) {
            $features = [];
            $icons = (array) $request->input('feature_icons', []);
            $titles = (array) $request->input('feature_titles', []);
            $descs = (array) $request->input('feature_descs', []);

            foreach ($titles as $idx => $title) {
                $title = trim($title);
                if ($title !== '') {
                    $features[] = [
                        'icon' => trim($icons[$idx] ?? 'fas fa-shield-alt'),
                        'title' => $title,
                        'description' => trim($descs[$idx] ?? ''),
                    ];
                }
            }
            SiteSetting::setValue('service_features', json_encode($features));
        }

        if ($request->has('footer_col1_titles')) {
            $lines = [];
            $titles = (array) $request->input('footer_col1_titles', []);
            $urls = (array) $request->input('footer_col1_urls', []);
            foreach ($titles as $idx => $t) {
                $t = trim($t);
                $u = trim($urls[$idx] ?? '');
                if ($t !== '') {
                    $lines[] = "{$t}|{$u}";
                }
            }
            SiteSetting::setValue('footer_col1_links', implode("\n", $lines));
        }

        if ($request->has('footer_col2_titles')) {
            $lines = [];
            $titles = (array) $request->input('footer_col2_titles', []);
            $urls = (array) $request->input('footer_col2_urls', []);
            foreach ($titles as $idx => $t) {
                $t = trim($t);
                $u = trim($urls[$idx] ?? '');
                if ($t !== '') {
                    $lines[] = "{$t}|{$u}";
                }
            }
            SiteSetting::setValue('footer_col2_links', implode("\n", $lines));
        }

        return redirect()->route('admin.settings.store')->with('success', 'Pengaturan toko berhasil diperbarui.');
    }

    public function integrations()
    {
        $settings = [];

        try {
            if (Schema::hasTable('site_settings')) {
                $settings = SiteSetting::getMany([
                    'midtrans_server_key',
                    'midtrans_client_key',
                    'midtrans_merchant_id',
                    'midtrans_environment',
                    'midtrans_is_active',
                    'rajaongkir_api_key',
                    'rajaongkir_origin_city',
                    'rajaongkir_api_type',
                    'rajaongkir_is_active',
                ]);
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        return view('admin.setting.integrations', compact('settings'));
    }

    public function updateIntegrations(Request $request)
    {
        $validated = $request->validate([
            'midtrans_server_key' => 'nullable|string',
            'midtrans_client_key' => 'nullable|string',
            'midtrans_merchant_id' => 'nullable|string',
            'midtrans_environment' => 'nullable|string|in:sandbox,production',
            'midtrans_is_active' => 'nullable|in:0,1',
            'rajaongkir_api_key' => 'nullable|string',
            'rajaongkir_origin_city' => 'nullable|string',
            'rajaongkir_api_type' => 'nullable|string|in:starter,basic,pro',
            'rajaongkir_is_active' => 'nullable|in:0,1',
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::setValue($key, $value);
        }

        return redirect()->route('admin.settings.integrations')->with('success', 'Pengaturan Pembayaran & Ongkir berhasil disimpan.');
    }
}
