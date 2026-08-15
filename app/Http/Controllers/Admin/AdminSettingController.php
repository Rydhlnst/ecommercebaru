<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminSettingController extends Controller
{
    public function policy()
    {
        $policies = collect();

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
        $settings = collect();

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

        return view('admin.setting.store', compact('settings'));
    }

    public function updateStore(Request $request)
    {
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
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::setValue($key, $value);
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
        $settings = collect();

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
