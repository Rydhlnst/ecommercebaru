<?php

use App\Providers\AppServiceProvider;
use Beres\Account\Providers\AccountServiceProvider;
use Beres\Checkout\Providers\CheckoutServiceProvider as BeresCheckoutServiceProvider;
use Beres\Customer\Providers\CustomerServiceProvider as BeresCustomerServiceProvider;
use Beres\Dashboard\Providers\DashboardServiceProvider;
use Beres\Highlight\Providers\HighlightServiceProvider;
use Beres\Inventory\Providers\InventoryServiceProvider as BeresInventoryServiceProvider;
use Beres\Notification\Providers\NotificationServiceProvider as BeresNotificationServiceProvider;
use Beres\Order\Providers\OrderServiceProvider;
use Beres\Payment\Providers\PaymentServiceProvider as BeresPaymentServiceProvider;
use Beres\Permission\Providers\PermissionServiceProvider;
use Beres\Product\Providers\ProductServiceProvider as BeresProductServiceProvider;
use Beres\Reports\Providers\ReportServiceProvider;
use Beres\Settings\Providers\SettingServiceProvider;
use Beres\Shipping\Providers\ShippingServiceProvider as BeresShippingServiceProvider;
use Webkul\Admin\Providers\AdminServiceProvider;
use Webkul\Attribute\Providers\AttributeServiceProvider;
use Webkul\BookingProduct\Providers\BookingProductServiceProvider;
use Webkul\CartRule\Providers\CartRuleServiceProvider;
use Webkul\CatalogRule\Providers\CatalogRuleServiceProvider;
use Webkul\Category\Providers\CategoryServiceProvider;
use Webkul\Checkout\Providers\CheckoutServiceProvider;
use Webkul\CMS\Providers\CMSServiceProvider;
use Webkul\Core\Providers\CoreServiceProvider;
use Webkul\Core\Providers\EnvValidatorServiceProvider;
use Webkul\Customer\Providers\CustomerServiceProvider;
use Webkul\DataGrid\Providers\DataGridServiceProvider;
use Webkul\DataTransfer\Providers\DataTransferServiceProvider;
use Webkul\DebugBar\Providers\DebugBarServiceProvider;
use Webkul\EUWithdrawal\Providers\EUWithdrawalServiceProvider;
use Webkul\FPC\Providers\FPCServiceProvider;
use Webkul\GDPR\Providers\GDPRServiceProvider;
use Webkul\ImageCache\Providers\ImageCacheServiceProvider;
use Webkul\Installer\Providers\InstallerServiceProvider;
use Webkul\Inventory\Providers\InventoryServiceProvider;
use Webkul\MagicAI\Providers\MagicAIServiceProvider;
use Webkul\Marketing\Providers\MarketingServiceProvider;
use Webkul\Notification\Providers\NotificationServiceProvider;
use Webkul\Payment\Providers\PaymentServiceProvider;
use Webkul\Paypal\Providers\PaypalServiceProvider;
use Webkul\PayU\Providers\PayUServiceProvider;
use Webkul\PhonePe\Providers\PhonePeServiceProvider;
use Webkul\Product\Providers\ProductServiceProvider;
use Webkul\Razorpay\Providers\RazorpayServiceProvider;
use Webkul\RMA\Providers\RMAServiceProvider;
use Webkul\Rule\Providers\RuleServiceProvider;
use Webkul\Sales\Providers\SalesServiceProvider;
use Webkul\Shipping\Providers\ShippingServiceProvider;
use Webkul\Shop\Providers\ShopServiceProvider;
use Webkul\Sitemap\Providers\SitemapServiceProvider;
use Webkul\SocialLogin\Providers\SocialLoginServiceProvider;
use Webkul\SocialShare\Providers\SocialShareServiceProvider;
use Webkul\Stripe\Providers\StripeServiceProvider;
use Webkul\Tax\Providers\TaxServiceProvider;
use Webkul\Theme\Providers\ThemeServiceProvider;
use Webkul\User\Providers\UserServiceProvider;

return [
    /**
     * Application service providers.
     */
    AppServiceProvider::class,

    /**
     * Webkul's service providers.
     */
    AdminServiceProvider::class,
    AttributeServiceProvider::class,
    BookingProductServiceProvider::class,
    CMSServiceProvider::class,
    CartRuleServiceProvider::class,
    CatalogRuleServiceProvider::class,
    CategoryServiceProvider::class,
    CheckoutServiceProvider::class,
    CoreServiceProvider::class,
    EnvValidatorServiceProvider::class,
    CustomerServiceProvider::class,
    DataGridServiceProvider::class,
    DataTransferServiceProvider::class,
    DebugBarServiceProvider::class,
    EUWithdrawalServiceProvider::class,
    FPCServiceProvider::class,
    GDPRServiceProvider::class,
    ImageCacheServiceProvider::class,
    InstallerServiceProvider::class,
    InventoryServiceProvider::class,
    MagicAIServiceProvider::class,
    MarketingServiceProvider::class,
    NotificationServiceProvider::class,
    PayUServiceProvider::class,
    PaymentServiceProvider::class,
    PaypalServiceProvider::class,
    PhonePeServiceProvider::class,
    ProductServiceProvider::class,
    RMAServiceProvider::class,
    RazorpayServiceProvider::class,
    RuleServiceProvider::class,
    SalesServiceProvider::class,
    ShippingServiceProvider::class,
    ShopServiceProvider::class,
    SitemapServiceProvider::class,
    SocialLoginServiceProvider::class,
    SocialShareServiceProvider::class,
    StripeServiceProvider::class,
    TaxServiceProvider::class,
    ThemeServiceProvider::class,
    UserServiceProvider::class,

    /**
     * Beres custom packages.
     */
    DashboardServiceProvider::class,
    BeresProductServiceProvider::class,
    BeresCustomerServiceProvider::class,
    OrderServiceProvider::class,
    BeresInventoryServiceProvider::class,
    BeresPaymentServiceProvider::class,
    BeresShippingServiceProvider::class,
    BeresCheckoutServiceProvider::class,
    AccountServiceProvider::class,
    BeresNotificationServiceProvider::class,
    PermissionServiceProvider::class,
    ReportServiceProvider::class,
    SettingServiceProvider::class,
    HighlightServiceProvider::class,
];
