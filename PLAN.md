# Beres Commerce - Development Plan

## Project Status

- **Base Framework**: Bagisto v2.4.8 (Laravel 12)
- **Local Server**: Laragon (PHP 8.4.21, MySQL 8.4.3, PHPMyAdmin 6)
- **Database**: beres_commerce
- **Admin URL**: http://localhost/admin
- **Admin Credentials**: admin@example.com / admin123
- **PHPMyAdmin URL**: http://localhost/phpmyadmin6

---

## Laragon Setup

### Quick Start

1. Buka **Laragon** → klik **Start All**
2. Akses:
   - Website: http://localhost
   - Admin: http://localhost/admin
   - PHPMyAdmin: http://localhost/phpmyadmin6
3. Untuk CLI, gunakan terminal bawaan Laragon (cmder) atau PowerShell

### Laragon Services

| Service | Status | Port |
|---------|--------|------|
| Apache/Nginx | Running | 80 |
| MySQL 8.4.3 | Running | 3306 |
| PHP 8.4.21 | - | - |
| PHPMyAdmin 6 | - | http://localhost/phpmyadmin6 |

### Common Commands (Laragon Terminal)

```bash
# Start all services
net start Apache2.4
net start MySQL8.4

# Or use Laragon GUI: Right-click → MySQL → Start

# Database access
mysql -u root

# PHP path (if needed explicitly)
C:\laragon\bin\php\php-8.4.21-Win32-vs17-x64\php.exe artisan

# Composer (Laragon includes Composer)
composer install
composer dump-autoload

# Node.js (for frontend build)
npm install
npm run build
```

### Project Location

Default Laragon project path: `C:\laragon\www\`

Current project: `D:\Projects\Freelance\ecommerce\`

**Note**: Jika project dipindah ke `C:\laragon\www\`, akses via:
- http://localhost/ecommerce
- http://localhost/ecommerce/admin

---

## Phase 0: UI/UX Customization (Bellroy Reference)

### Design Reference: Bellroy.com

**Scraped with Firecrawl MCP** - Analysis of homepage, collection, and product pages.

### Bellroy Design Patterns

#### 1. Color Palette

| Element | Color | Usage |
|---------|-------|-------|
| Background | `#FFFFFF` | Clean white base |
| Text Primary | `#1A1A1A` | Headings, body text |
| Text Secondary | `#6B7280` | Descriptions, meta |
| Accent | `#2D5A27` | CTAs, links, highlights |
| Surface | `#F9FAFB` | Card backgrounds |
| Border | `#E5E7EB` | Dividers, borders |

#### 2. Typography

| Element | Font | Weight | Size |
|---------|------|--------|------|
| Headings | Inter | 600-700 | 24-48px |
| Body | Inter | 400 | 14-16px |
| CTAs | Inter | 500 | 14px |
| Price | Inter | 600 | 18-24px |

#### 3. Homepage Structure (Bellroy Pattern)

```
┌─────────────────────────────────────────────┐
│ Top Bar: Free shipping + Country selector   │
├─────────────────────────────────────────────┤
│ Header: Logo | Search | Cart | Account      │
├─────────────────────────────────────────────┤
│ Hero Banner: Full-width lifestyle image     │
│ + Bold headline + CTA button               │
├─────────────────────────────────────────────┤
│ Value Props: 4 icons (Shipping, Returns...) │
├─────────────────────────────────────────────┤
│ Featured Categories: Grid of 4-6 cards     │
├─────────────────────────────────────────────┤
│ Product Carousel: Best Sellers              │
├─────────────────────────────────────────────┤
│ Lifestyle Banner: Brand story image         │
├─────────────────────────────────────────────┤
│ Social Proof: Instagram feed / Reviews      │
├─────────────────────────────────────────────┤
│ Newsletter: Email signup                    │
├─────────────────────────────────────────────┤
│ Footer: Links + Contact + Social            │
└─────────────────────────────────────────────┘
```

#### 4. Key UI Elements

**Header:**
- Minimal, sticky on scroll
- Logo left, search center, icons right
- Clean search bar with rounded corners
- Cart badge with item count

**Product Cards:**
- Clean white background
- Product image with hover zoom
- Product name (2 lines max)
- Price with strikethrough for discount
- Color swatches below
- "Add to Cart" on hover

**Hero Section:**
- Full-width image
- Overlay text with CTA
- Mobile: stacked layout

**Footer:**
- 4-column layout
- Newsletter signup
- Social media icons
- Payment icons
- Copyright

### Customization Tasks

#### 5. Theme Override Setup

- [ ] Create `resources/themes/default/views/` directory
- [ ] Copy key templates to override
- [ ] Update Tailwind config with Bellroy colors
- [ ] Add Inter font family

#### 6. Header Customization

- [ ] Simplify top bar (remove currency switcher, keep shipping message)
- [ ] Clean logo area with better spacing
- [ ] Minimal search bar with icon
- [ ] Cart icon with badge
- [ ] Sticky header on scroll

#### 7. Homepage Customization

- [ ] Hero banner with lifestyle image
- [ ] Value props bar (Free Shipping, Easy Returns, Secure Payment, 24/7 Support)
- [ ] Featured categories grid
- [ ] Product carousel (Best Sellers)
- [ ] Brand story section
- [ ] Testimonials/Social proof
- [ ] Newsletter signup

#### 8. Product Card Customization

- [ ] Clean card design with hover effects
- [ ] Image zoom on hover
- [ ] Product name with line clamp
- [ ] Price formatting (IDR)
- [ ] Color swatches
- [ ] Quick add to cart button

#### 9. Footer Customization

- [ ] 4-column layout
- [ ] Newsletter signup form
- [ ] Social media links
- [ ] Payment method icons
- [ ] Indonesian language content

#### 10. Admin Panel Customization

- [ ] Custom login page branding
- [ ] Dashboard widgets cleanup
- [ ] Color scheme update
- [ ] Logo replacement

### File Locations for Overrides

```
resources/themes/default/views/
├── components/
│   ├── layouts/
│   │   ├── index.blade.php           # Master layout
│   │   ├── header/
│   │   │   ├── desktop/
│   │   │   │   ├── index.blade.php   # Desktop header
│   │   │   │   ├── top.blade.php     # Top bar
│   │   │   │   └── bottom.blade.php  # Main header
│   │   │   └── mobile/
│   │   │       └── index.blade.php   # Mobile header
│   │   └── footer/
│   │       └── index.blade.php       # Footer
│   └── products/
│       ├── card.blade.php            # Product card
│       └── carousel.blade.php        # Product carousel
├── home/
│   └── index.blade.php               # Homepage
└── components/
    └── carousel/
        └── index.blade.php           # Image carousel
```

### Tailwind Config Updates

```javascript
// tailwind.config.js
colors: {
    primary: {
        50: '#f0fdf4',
        100: '#dcfce7',
        200: '#bbf7d0',
        300: '#86efac',
        400: '#4ade80',
        500: '#2D5A27',  // Bellroy green
        600: '#1a4314',
        700: '#166534',
        800: '#14532d',
        900: '#052e16',
    },
    neutral: {
        50: '#F9FAFB',
        100: '#F3F4F6',
        200: '#E5E7EB',
        300: '#D1D5DB',
        400: '#9CA3AF',
        500: '#6B7280',
        600: '#4B5563',
        700: '#374151',
        800: '#1F2937',
        900: '#111827',
    }
},
fontFamily: {
    sans: ['Inter', 'system-ui', 'sans-serif'],
}
```

---

### 1.1 Project Structure

```
packages/Beres/
├── Payment/          # Payment gateway integration
├── Shipping/         # RajaOngkir integration
├── Theme/            # Custom storefront theme
└── Notification/     # Email & WhatsApp notifications
```

### 1.2 Environment Configuration

- [x] Bagisto installed
- [x] Database configured (beres_commerce)
- [x] Timezone set to Asia/Jakarta
- [x] Currency set to IDR
- [x] Locale set to id (Indonesian)

### Laragon PHP Extensions (Already Enabled)

- [x] intl
- [x] zip
- [x] curl
- [x] mbstring
- [x] gd
- [x] bcmath
- [x] pdo_mysql
- [x] sqlite3

### .env Configuration

```env
APP_NAME="Beres Commerce"
APP_URL=http://localhost
APP_ADMIN_URL=admin
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_CURRENCY=IDR

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=beres_commerce
DB_USERNAME=root
DB_PASSWORD=
```

### 1.3 Package Setup

- [ ] Create Beres package namespace
- [ ] Register packages in `bootstrap/providers.php`
- [ ] Register models in `config/concord.php`
- [ ] Configure `config/themes.php` for custom theme

---

## Phase 2: Custom Theme Development

### 2.1 Theme Structure

```
packages/Beres/Theme/
├── src/
│   ├── Providers/
│   │   └── ThemeServiceProvider.php
│   ├── Resources/
│   │   ├── views/
│   │   │   ├── components/
│   │   │   ├── layouts/
│   │   │   ├── livewire/
│   │   │   └── shop/
│   │   └── assets/
│   │       ├── css/
│   │       ├── js/
│   │       └── images/
│   └── config/
│       └── theme.php
└── package.json
```

### 2.2 Frontend Pages

- [ ] Home Page
  - Hero Banner
  - Promotion Banner
  - Featured Products
  - Best Seller
  - New Arrival
  - Product Categories
  - Customer Testimonials
  - Newsletter
  - Footer

- [ ] Product Catalog
  - Product Listing
  - Category Navigation
  - Search
  - Filter by Category
  - Filter by Price
  - Filter by Stock
  - Sort Products

- [ ] Product Detail
  - Product Name
  - Gallery
  - Price / Discount
  - Stock / SKU / Weight
  - Description
  - Related Products
  - Quantity Selector
  - Add to Cart
  - Wishlist

- [ ] Shopping Cart
  - View Cart
  - Update Quantity
  - Remove Item
  - Order Summary

- [ ] Checkout
  - Customer Information
  - Shipping Address
  - Shipping Method
  - Payment Method
  - Order Summary

- [ ] Customer Account
  - Register / Login
  - Forgot Password
  - Profile
  - Address Book
  - Order History
  - Wishlist

- [ ] Blog
  - Article List
  - Categories
  - Detail Article

- [ ] Contact
  - Contact Form
  - WhatsApp Button
  - Email
  - Google Maps
  - FAQ

---

## Phase 3: Payment Integration

### 3.1 Package Structure

```
packages/Beres/Payment/
├── src/
│   ├── Providers/
│   │   └── PaymentServiceProvider.php
│   ├── Services/
│   │   ├── MidtransService.php
│   │   └── XenditService.php
│   ├── Controllers/
│   │   └── PaymentCallbackController.php
│   ├── Routes/
│   │   └── routes.php
│   └── Config/
│       └── payment.php
└── composer.json
```

### 3.2 Payment Methods

- [ ] QRIS
- [ ] Virtual Account
- [ ] E-Wallet
- [ ] Retail Outlet
- [ ] Credit Card (if supported)

### 3.3 Payment Flow

- [ ] Payment initiation
- [ ] Payment callback handling
- [ ] Webhook processing
- [ ] Order status synchronization
- [ ] Payment failure handling

---

## Phase 4: Shipping Integration

### 4.1 Package Structure

```
packages/Beres/Shipping/
├── src/
│   ├── Providers/
│   │   └── ShippingServiceProvider.php
│   ├── Services/
│   │   └── RajaOngkirService.php
│   ├── Controllers/
│   │   └── ShippingController.php
│   ├── Routes/
│   │   └── routes.php
│   └── Config/
│       └── shipping.php
└── composer.json
```

### 4.2 Shipping Features

- [ ] City/District lookup
- [ ] Shipping cost calculation
- [ ] Courier selection (JNE, J&T, SiCepat, etc.)
- [ ] Service selection (Regular, Express, Same Day)
- [ ] Estimated delivery display

### 4.3 Shipping Flow

- [ ] Address validation
- [ ] Cost calculation API
- [ ] Courier integration
- [ ] Tracking integration

---

## Phase 5: Notification System

### 5.1 Package Structure

```
packages/Beres/Notification/
├── src/
│   ├── Providers/
│   │   └── NotificationServiceProvider.php
│   ├── Services/
│   │   ├── EmailService.php
│   │   └── WhatsAppService.php
│   ├──Listeners/
│   │   ├── OrderCreatedListener.php
│   │   ├── PaymentSuccessListener.php
│   │   └── OrderShippedListener.php
│   └── Config/
│       └── notification.php
└── composer.json
```

### 5.2 Email Notifications

- [ ] Order Created
- [ ] Payment Success
- [ ] Order Shipped
- [ ] Order Delivered

### 5.3 WhatsApp Notifications (Optional)

- [ ] WhatsApp Business API integration
- [ ] Order status updates
- [ ] Shipping notifications

---

## Phase 6: Admin Customization

### 6.1 Admin Theme

- [ ] Custom admin logo
- [ ] Custom color scheme
- [ ] Indonesian language pack

### 6.2 Admin Features (Reuse Bagisto)

- [ ] Dashboard
- [ ] Product Management
- [ ] Category Management
- [ ] Customer Management
- [ ] Inventory Management
- [ ] Order Management
- [ ] CMS Pages
- [ ] Configuration

---

## Phase 7: SEO Optimization

### 7.1 SEO Features

- [ ] Meta Title
- [ ] Meta Description
- [ ] URL Slugs
- [ ] Sitemap Generation
- [ ] Robots.txt
- [ ] Open Graph Tags

---

## Phase 8: Performance Optimization

### 8.1 Optimization Tasks

- [ ] Image optimization
- [ ] CSS/JS minification
- [ ] Database query optimization
- [ ] Cache configuration
- [ ] CDN setup (optional)

---

## Phase 9: Testing & Deployment

### 9.1 Testing

- [ ] Unit tests
- [ ] Integration tests
- [ ] E2E tests (Playwright)
- [ ] Payment gateway testing
- [ ] Shipping API testing

### 9.2 Deployment

- [ ] Production environment setup
- [ ] SSL certificate
- [ ] Domain configuration
- [ ] CI/CD pipeline
- [ ] Monitoring setup

---

## Development Rules

1. Never rewrite existing Bagisto functionality
2. Always check if feature exists before implementing
3. Prefer extension over modification
4. Keep all custom code modular
5. Maintain compatibility with future Bagisto updates
6. Follow Laravel and Bagisto best practices
7. Stay within defined project scope
8. Prioritize maintainability and reusability

---

## File Structure Summary

```
ecommerce/
├── app/                    # Thin Laravel app shell
├── bootstrap/
│   ├── app.php
│   └── providers.php       # Register custom packages here
├── config/
│   ├── concord.php         # Register models here
│   └── themes.php          # Theme configuration
├── packages/
│   └── Beres/
│       ├── Payment/        # Midtrans/Xendit integration
│       ├── Shipping/       # RajaOngkir integration
│       ├── Theme/          # Custom storefront
│       └── Notification/   # Email & WhatsApp
├── public/
│   └── storage/            # Symlinked to storage/app/public
├── routes/
├── storage/
└── vendor/                 # Do not edit
```

---

## Next Steps

1. Create package directory structure
2. Build custom theme foundation
3. Implement payment gateway integration
4. Integrate RajaOngkir shipping
5. Build notification system
6. Test all integrations
7. Deploy to production

---

## Troubleshooting

### MySQL not starting
- Buka Laragon → Right-click → MySQL → Start
- Atau: `net start MySQL8.4`

### Port 3306 already in use
```bash
# Cari proses yang menggunakan port 3306
netstat -ano | findstr :3306
# Stop proses tersebut
taskkill /PID <PID> /F
```

### PHP extensions tidak aktif
- Buka `C:\laragon\bin\php\php-8.4.21-Win32-vs17-x64\php.ini`
- Pastikan `extension=intl` dan `extension=zip` tidak dikomentari

### PHPMyAdmin 6 tidak bisa diakses
- Pastikan Apache/Nginx running di Laragon
- Akses: http://localhost/phpmyadmin6

### Storage link error
```bash
php artisan storage:link
```

### Clear cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
composer dump-autoload
```
