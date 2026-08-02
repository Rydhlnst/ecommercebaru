# Ecommerce Codebase Verification Prompt

Use this prompt with another LLM (Claude, GPT-4, Gemini) to verify the codebase.

## Context
Bagisto 2.4.x (Laravel 12, PHP 8.3) ecommerce at D:\Projects\Freelance\ecommerce.
Docker-based deployment. Custom theme overrides Shop package views.

## What I Need You To Verify
Read each file listed below and answer the questions. Be specific — cite line numbers.
If something is broken, suggest the exact fix.

---

## 1. ENTRYPOINT & CACHE (Docker)

**Files:**
- `docker/entrypoint.sh`
- `Dockerfile`

**Questions:**
- [ ] Does `entrypoint.sh` clear `view:clear` and `responsecache:clear` BEFORE `config:cache`?
- [ ] Does `Dockerfile` resolve Windows symlinks in `vendor/beres/` before the Node build stage?
- [ ] Is `.public-snapshot` copied to `public/` volume on container start?

---

## 2. PRODUCT CONFIGURABLE VARIANT SYSTEM

**Files:**
- `packages/Webkul/Installer/src/Database/Seeders/Attribute/AttributeTableSeeder.php` (search for `net_weight`)
- `packages/Webkul/Installer/src/Database/Seeders/Attribute/AttributeOptionTableSeeder.php` (search for `attribute_id => 35`)
- `packages/Webkul/Installer/src/Database/Seeders/ProductTableSeeder.php` (search for `FOOD_ATTRIBUTE_CODES`)
- `packages/Webkul/Installer/src/Data/demo-products.json` (search for `super_attributes`)

**Questions:**
- [ ] Is `net_weight` attribute type `select` with `is_configurable => 1`?
- [ ] Is `swatch_type` set to `text` via separate `DB::table('attributes')->where('code','net_weight')->update(...)` after bulk insert? (NOT in the bulk insert array — that causes column count mismatch)
- [ ] Are there 6 options for net_weight (200g, 250g, 500g, 500ml, 1000g, 1 KG)?
- [ ] Does `demo-products.json` have `super_attributes` with `swatch_type: "text"` and correct option IDs (39=500g, 41=1000g)?
- [ ] Do all product `net_weight` values in JSON reference option IDs (integers) not text strings?
- [ ] Does `ProductTableSeeder` have `FOOD_ATTRIBUTE_CODES` array and process them in `buildSingleProductData()`?

---

## 3. FRONTEND PRODUCT CARD

**Files:**
- `resources/themes/default/views/components/layouts/_product-card.blade.php`

**Questions:**
- [ ] Does the card detect configurable products and show child variants as pill buttons?
- [ ] Is there a `beres-variant-input` hidden input that gets updated when a variant is clicked?
- [ ] Does the card have `data-super-attr-id` attribute for the add-to-cart AJAX?
- [ ] Does sale price show original price strikethrough + new price + SALE badge?
- [ ] Does the quantity stepper (+/-) and Add To Cart button work for configurable products?

---

## 4. SEARCH SYSTEM

**Files:**
- `resources/themes/default/views/components/layouts/index.blade.php` (search for `normalizeQuery`)
- `resources/themes/default/views/components/layouts/header/desktop/bottom.blade.php` (search for `search`)

**Questions:**
- [ ] Is there a `normalizeQuery` function that lowercases and strips symbols?
- [ ] Is there 500ms debounce on search input?
- [ ] Does Enter key submit immediately (skip debounce)?
- [ ] Is `pattern="[^\\]+"` removed from the search input?

---

## 5. FOOTER — EDITABLE FROM ADMIN

**Files:**
- `resources/themes/default/views/components/layouts/footer/index.blade.php`
- `packages/Webkul/Admin/src/Resources/views/settings/themes/edit/footer-links.blade.php`

**Questions:**
- [ ] Does the footer load data from `$customization` (Theme Customization)?
- [ ] Are there 4 columns supported (column_1 through column_4)?
- [ ] Is column_4 for social media with auto-detect icon (facebook, instagram, youtube, tiktok, whatsapp, twitter)?
- [ ] Do fallback defaults exist if no customization data?
- [ ] Does the admin footer-links view have `<option value="column_4">4 (Social Media)</option>`?
- [ ] Is the `for` loop `i <= 4` (not `i <= 3`)?

---

## 6. HEADER — LOGO, SEARCH, LANGUAGE DROPDOWN

**Files:**
- `resources/themes/default/views/components/layouts/header/desktop/bottom.blade.php`
- `packages/Webkul/Shop/src/Resources/views/components/layouts/header/mobile/index.blade.php`

**Questions:**
- [ ] Does desktop header have: logo (PNG) | search bar (full width, `flex-1`) | EN/ID dropdown | cart | profile?
- [ ] Is EN/ID a dropdown (x-shop::dropdown) with SVG flag icons (GB/ID)?
- [ ] Does mobile header have SVG flag select dropdown?
- [ ] Is there NO `overflow-x-auto` on category dropdowns?
- [ ] Is the search bar `w-full` (no `max-w-4xl`)?
- [ ] Is image search (`@include('shop::search.images.index')`) removed?

---

## 7. MAIN LAYOUT — FAVICON, TRANSLATE, SCROLLBAR

**Files:**
- `resources/themes/default/views/components/layouts/index.blade.php`

**Questions:**
- [ ] Is favicon `<link rel="icon" type="image/png" href="/images/ankesh-mart-logo.png">`?
- [ ] Is Google Translate CSS (hide banner) present?
- [ ] Is custom scrollbar CSS present (`::-webkit-scrollbar`, `scrollbar-width: thin`)?
- [ ] Is Google Translate init script present with `pageLanguage: 'en'`, `includedLanguages: 'en,id'`?
- [ ] Is `setGoogleTranslateLang` function defined?
- [ ] Is there re-translate logic after Vue mount (for dynamic content)?
- [ ] Is the flag icon updated via JS based on `googtrans` cookie?

---

## 8. MAP SECTION + FOOTER STYLING

**Files:**
- `resources/themes/default/views/components/layouts/map-section.blade.php`
- `resources/themes/default/views/components/layouts/footer/index.blade.php`

**Questions:**
- [ ] Is map section background white (`bg-white` or `background-color:#fff`)?
- [ ] Is map iframe `rounded-none` (no border-radius)?
- [ ] Is map aspect ratio `aspect-video` (16:9)?
- [ ] Is footer background solid green `#2D5A27` (not gradient)?
- [ ] Is there NO `mt-16` or `mt-0` on footer element?

---

## 9. HOMEPAGE — CATEGORIES, HERO, PRODUCTS

**Files:**
- `resources/themes/default/views/home/index.blade.php`

**Questions:**
- [ ] Is category grid a horizontal scroll row (`flex overflow-x-auto`) not a grid?
- [ ] Does each product card include pass `'index'=>$i` for numbered badge?
- [ ] Do review links point to Google Review URL (not internal review page)?
- [ ] Is the hero section using `width:100%` (not `width:100vw`) to avoid horizontal scroll?

---

## 10. ADMIN CONFIG — MIDTRANS, RAJAONGKIR, RESEND

**Files:**
- `packages/Beres/Settings/src/Config/system.php`

**Questions:**
- [ ] Is there a `beres_storefront.midtrans` section with: active, server_key, client_key, merchant_id, environment (sandbox/production), payment_types?
- [ ] Is there a `beres_storefront.shipping` section with: active, api_key, api_type (starter/basic/pro), origin_city, couriers?
- [ ] Is there a `beres_storefront.email` section with: api_key, from_email, from_name?
- [ ] Is there a `beres_storefront.contact` section with: google_review_url, google_place_id, google_api_key?

---

## 11. DEPLOY SCRIPT

**File:**
- `deploy.sh`

**Questions:**
- [ ] Does it stop ALL containers first (`docker stop $(docker ps -q)`)?
- [ ] Does it run `migrate:fresh` before seeding?
- [ ] Does it seed in correct order: Attribute → Category → Locales → Currency → Countries → States → Config → CustomerGroup → InventorySource → Channel → Roles → Admins → ThemeCustomization → Product?
- [ ] Does it clear ALL caches after seeding?
- [ ] Does it verify products count, logo HTTP status, categories, customer groups, channels?

---

## 12. BLADE SYNTAX SAFETY

Run `grep -r '{{ $' resources/themes/ packages/Webkul/Shop/src/Resources/views/ --include='*.blade.php'` and check:
- [ ] Are there any `{{ }}` (escaped) used where `{!! !!}` (unescaped) is needed for HTML content?
- [ ] Are textarea attribute values rendered with `{!! !!}` (not `{{ }}`) in the additional information tab?

---

## Output Format

For each section, respond with:
```
## Section N: [Status: PASS / WARNING / FAIL]

- [x] Question 1 — PASS: explanation
- [ ] Question 2 — FAIL: explanation + fix
```

At the end, provide a SUMMARY table:
| # | Section | Status | Critical Issues |
|---|---------|--------|-----------------|
| 1 | Entrypoint | PASS | — |
| 2 | Variants | FAIL | swatch_type in bulk insert |
