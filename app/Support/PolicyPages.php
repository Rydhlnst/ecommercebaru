<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PolicyPages
{
    /**
     * Policy settings and their public Bagisto CMS page metadata.
     */
    public static function definitions(): array
    {
        return [
            'policy_privacy' => [
                'title' => 'Privacy Policy',
                'url_key' => 'privacy-policy',
                'icon' => 'fas fa-shield-alt',
                'icon_class' => 'text-blue-500',
                'meta_keywords' => 'privacy, personal data',
                'content' => '<h2>Privacy Policy</h2><p>We protect the personal information you provide when using this store. Information is used only to process orders, provide support, improve services, and meet legal obligations.</p><h3>Information We Collect</h3><p>We may collect your name, contact details, delivery address, order details, and payment status. Payment credentials are handled by the selected payment provider and are not stored by this store.</p><h3>Your Choices</h3><p>You may request access to, correction of, or deletion of your personal information where permitted by law. Contact customer service for assistance.</p>',
            ],
            'policy_terms' => [
                'title' => 'Terms & Conditions',
                'url_key' => 'terms-conditions',
                'icon' => 'fas fa-file-contract',
                'icon_class' => 'text-purple-500',
                'meta_keywords' => 'terms, conditions, service',
                'content' => '<h2>Terms &amp; Conditions</h2><p>By using this store, you agree to provide accurate information, use the service lawfully, and comply with the policies published on this page.</p><h3>Orders</h3><p>An order is accepted only after payment and availability have been confirmed. We may cancel or adjust an order when product, pricing, stock, or delivery information is inaccurate.</p><h3>Changes</h3><p>We may update these terms when operational or legal requirements change. The latest version published on this page applies to future use of the store.</p>',
            ],
            'policy_payment' => [
                'title' => 'Payment Policy',
                'url_key' => 'payment-policy',
                'icon' => 'fas fa-credit-card',
                'icon_class' => 'text-emerald-500',
                'meta_keywords' => 'payment, billing, order',
                'content' => '<h2>Payment Policy</h2><p>Payment must be completed through the payment methods shown during checkout. Please use the payment reference and amount shown on the order confirmation.</p><h3>Verification</h3><p>Orders are processed after payment verification. If verification fails or expires, the order may be canceled automatically.</p><h3>Pricing</h3><p>Prices, promotions, delivery charges, and availability may change before an order is confirmed. The final amount is displayed before payment.</p>',
            ],
            'policy_shipping' => [
                'title' => 'Shipping Policy',
                'url_key' => 'shipping-policy',
                'icon' => 'fas fa-truck',
                'icon_class' => 'text-green-500',
                'meta_keywords' => 'shipping, delivery, courier',
                'content' => '<h2>Shipping Policy</h2><p>Delivery options, charges, and estimated arrival times are shown at checkout based on the delivery address and selected courier service.</p><h3>Delivery Details</h3><p>Customers are responsible for providing a complete and accurate delivery address and reachable contact number. Delivery estimates are not guaranteed and may be affected by courier operations, weather, or public holidays.</p><h3>Receipt</h3><p>Please inspect the package when it arrives. Report damaged, missing, or incorrect items promptly through customer service with supporting information.</p>',
            ],
            'policy_refund' => [
                'title' => 'Refund Policy',
                'url_key' => 'refund-policy',
                'icon' => 'fas fa-undo',
                'icon_class' => 'text-amber-500',
                'meta_keywords' => 'refund, payment, claim',
                'content' => '<h2>Refund Policy</h2><p>Refund requests are reviewed after the relevant order and supporting evidence have been checked. Approved refunds are returned through the appropriate method available for the original payment.</p><h3>Eligible Requests</h3><p>Requests may be considered for unavailable items, duplicate payments, verified delivery failures, or products that arrive damaged or materially different from the order.</p><h3>Processing Time</h3><p>Processing time depends on the payment provider and financial institution. Customer service will provide an update after the request has been reviewed.</p>',
            ],
            'policy_return' => [
                'title' => 'Return Policy',
                'url_key' => 'return-policy',
                'icon' => 'fas fa-box-open',
                'icon_class' => 'text-rose-500',
                'meta_keywords' => 'return, product, exchange',
                'content' => '<h2>Return Policy</h2><p>Please contact customer service before returning an item. Eligibility depends on product condition, product category, and the reason for the request.</p><h3>Return Conditions</h3><p>Items should be unused where applicable, complete with their original packaging, and accompanied by order information. Perishable, hygiene-sensitive, or customized goods may be excluded unless they arrive damaged or incorrect.</p><h3>Review</h3><p>We will confirm whether a return, replacement, or refund is appropriate after reviewing the request and evidence.</p>',
            ],
        ];
    }

    public static function settingKeys(): array
    {
        return array_keys(self::definitions());
    }

    public static function defaults(): array
    {
        return collect(self::definitions())
            ->mapWithKeys(fn (array $policy, string $key) => [$key => $policy['content']])
            ->all();
    }

    /**
     * Keep only policy-content markup that is safe to render on the storefront.
     */
    public static function sanitize(string $content): string
    {
        $content = strip_tags(trim($content), '<a><b><blockquote><br><em><h2><h3><h4><hr><i><li><ol><p><strong><u><ul>');
        $content = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $content) ?? '';
        $content = preg_replace('/\s+(?:href|src)\s*=\s*(?:"\s*(?:javascript|data):[^"]*"|\'\s*(?:javascript|data):[^\']*\'|(?:javascript|data):[^\s>]*)/i', '', $content) ?? '';

        return trim($content);
    }

    /**
     * Publish dashboard policy content to the CMS routes used by the storefront footer.
     */
    public static function sync(array $contents): void
    {
        if (! Schema::hasTable('cms_pages')
            || ! Schema::hasTable('cms_page_translations')
            || ! Schema::hasTable('cms_page_channels')
            || ! Schema::hasTable('channels')
            || ! Schema::hasTable('locales')) {
            return;
        }

        $locales = DB::table('locales')->pluck('code')->filter()->all();
        $channels = DB::table('channels')->pluck('id')->all();

        if ($locales === [] || $channels === []) {
            return;
        }

        DB::transaction(function () use ($contents, $locales, $channels) {
            foreach (self::definitions() as $settingKey => $policy) {
                $pageId = DB::table('cms_page_translations')
                    ->where('url_key', $policy['url_key'])
                    ->value('cms_page_id');

                if (! $pageId) {
                    $pageId = DB::table('cms_pages')->insertGetId([
                        'layout' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $content = self::sanitize($contents[$settingKey] ?? $policy['content']);

                foreach ($locales as $locale) {
                    DB::table('cms_page_translations')->updateOrInsert(
                        [
                            'cms_page_id' => $pageId,
                            'url_key' => $policy['url_key'],
                            'locale' => $locale,
                        ],
                        [
                            'page_title' => $policy['title'],
                            'html_content' => $content,
                            'meta_title' => $policy['title'],
                            'meta_description' => $policy['title'].' for this store.',
                            'meta_keywords' => $policy['meta_keywords'],
                        ]
                    );
                }

                foreach ($channels as $channelId) {
                    DB::table('cms_page_channels')->insertOrIgnore([
                        'cms_page_id' => $pageId,
                        'channel_id' => $channelId,
                    ]);
                }
            }
        });
    }
}
