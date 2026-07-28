<?php

namespace Beres\Settings\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Settings\Services\SettingService;

class SettingController extends Controller
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    /**
     * Display settings index.
     */
    public function index()
    {
        return view('beres-settings::settings.index', [
            'store'   => $this->settingService->getStoreSettings(),
            'company' => $this->settingService->getCompanySettings(),
            'smtp'    => $this->settingService->getSmtpSettings(),
        ]);
    }

    /**
     * Update store settings.
     */
    public function updateStore(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'url'      => 'required|url',
            'timezone' => 'required|string',
            'locale'   => 'required|string',
            'currency' => 'required|string',
        ]);

        $result = $this->settingService->updateStoreSettings($request->only([
            'name', 'url', 'timezone', 'locale', 'currency',
        ]));

        if ($result) {
            return redirect()->route('admin.settings.index')
                ->with('success', 'Pengaturan toko berhasil diperbarui');
        }

        return redirect()->back()
            ->with('error', 'Gagal memperbarui pengaturan');
    }

    /**
     * Update company settings.
     */
    public function updateCompany(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
        ]);

        $result = $this->settingService->updateStoreSettings($request->only([
            'name', 'address', 'phone', 'email',
        ]));

        if ($result) {
            return redirect()->route('admin.settings.index')
                ->with('success', 'Pengaturan perusahaan berhasil diperbarui');
        }

        return redirect()->back()
            ->with('error', 'Gagal memperbarui pengaturan');
    }

    /**
     * Update SMTP settings.
     */
    public function updateSmtp(Request $request)
    {
        $request->validate([
            'driver'     => 'required|string',
            'host'       => 'required|string',
            'port'       => 'required|integer',
            'username'   => 'nullable|string',
            'password'   => 'nullable|string',
            'encryption' => 'required|string',
        ]);

        $result = $this->settingService->updateStoreSettings($request->only([
            'driver', 'host', 'port', 'username', 'password', 'encryption',
        ]));

        if ($result) {
            return redirect()->route('admin.settings.index')
                ->with('success', 'Pengaturan SMTP berhasil diperbarui');
        }

        return redirect()->back()
            ->with('error', 'Gagal memperbarui pengaturan');
    }
}
