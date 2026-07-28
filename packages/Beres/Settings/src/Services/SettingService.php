<?php

namespace Beres\Settings\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    /**
     * Get setting value.
     */
    public function get(string $key, $default = null)
    {
        return Config::get($key, $default);
    }

    /**
     * Update setting value.
     */
    public function set(string $key, $value): bool
    {
        // Store in database or config
        return true;
    }

    /**
     * Get all settings for a group.
     */
    public function getGroup(string $group): array
    {
        return Config::get("beres-settings.{$group}", []);
    }

    /**
     * Get store settings.
     */
    public function getStoreSettings(): array
    {
        return [
            'name'        => config('app.name', 'Beres Commerce'),
            'url'         => config('app.url', 'http://localhost'),
            'admin_url'   => config('app.admin_url', 'admin'),
            'timezone'    => config('app.timezone', 'Asia/Jakarta'),
            'locale'      => config('app.locale', 'id'),
            'currency'    => config('app.currency', 'IDR'),
        ];
    }

    /**
     * Get company settings.
     */
    public function getCompanySettings(): array
    {
        return [
            'name'    => config('app.name'),
            'address' => '',
            'phone'   => '',
            'email'   => '',
            'website' => config('app.url'),
        ];
    }

    /**
     * Get SMTP settings.
     */
    public function getSmtpSettings(): array
    {
        return [
            'driver'   => config('mail.default', 'smtp'),
            'host'     => config('mail.mailers.smtp.host', '127.0.0.1'),
            'port'     => config('mail.mailers.smtp.port', 2525),
            'username' => config('mail.mailers.smtp.username', ''),
            'password' => config('mail.mailers.smtp.password', ''),
            'encryption' => config('mail.mailers.smtp.encryption', 'tls'),
        ];
    }

    /**
     * Get invoice settings.
     */
    public function getInvoiceSettings(): array
    {
        return [
            'prefix' => 'INV-',
            'footer' => 'Terima kasih telah berbelanja di ' . config('app.name'),
        ];
    }

    /**
     * Update store settings.
     */
    public function updateStoreSettings(array $data): bool
    {
        // Update config values
        foreach ($data as $key => $value) {
            config(["beres-settings.store.{$key}" => $value]);
        }

        return true;
    }
}
