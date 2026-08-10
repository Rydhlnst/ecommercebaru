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
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::setValue($key, $value);
        }

        return redirect()->route('admin.settings.store')->with('success', 'Pengaturan toko berhasil diperbarui.');
    }
}
