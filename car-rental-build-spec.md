# Car Rental Website + Admin Panel — Build Spec for Claude Code

You are building a production car rental website with an admin panel, end to end, in one Laravel application. Read this whole file before writing code. Work milestone by milestone (section 14), commit after each milestone, and do not move on until that milestone's checks pass.

---

## 1. Goal

A responsive public website where customers browse a car fleet and submit **booking requests** (not paid bookings), plus contact via **WhatsApp**. A **Filament admin panel** lets the business manage cars, categories, bookings and **all website text and images without code changes**.

- Launch language: **English only**.
- Must be **multilingual-ready**: Arabic (RTL), Russian and others will be added later with no schema or code restructuring (see section 5).

---

## 2. Tech stack

| Layer | Choice |
| --- | --- |
| Framework | Latest stable Laravel, PHP 8.2+ |
| Admin | Latest stable Filament (panel at `/admin`) |
| Frontend | Blade + Tailwind CSS (Vite) + Alpine.js |
| DB | MySQL 8 (dev may use MySQL or SQLite; production MySQL) |
| Media | `spatie/laravel-medialibrary` (+ Filament media plugin) |
| Settings | `spatie/laravel-settings` (+ Filament settings plugin) |
| Translations | `spatie/laravel-translatable` (+ Filament translatable plugin installed but locales limited to `en`) |
| Roles | `spatie/laravel-permission` + `bezhansalleh/filament-shield` |
| Activity log | `spatie/laravel-activitylog` |
| SEO | `spatie/laravel-sitemap` |
| Backups | `spatie/laravel-backup` |
| Anti-spam | Honeypot field + rate limiting + Cloudflare Turnstile (keys from settings; skip check if keys empty) |
| Tests | Pest |

Use the versions of each package that are compatible with the installed Laravel and Filament versions. Check compatibility before installing; do not force incompatible versions.

---

## 3. Conventions

- PSR-12, typed properties and return types, `final` classes where sensible.
- Business logic in Actions/Services (`app/Actions`, `app/Services`), not in controllers or Blade.
- Enums (`app/Enums`) for fixed sets: `CarStatus`, `BookingStatus`, `Transmission`, `FuelType`, `AddonPriceType`.
- Form Requests for all public form validation.
- **No hardcoded user-facing strings in Blade, mail or PHP.** Static UI text uses `__('key')` from `lang/en/*.php`. Content text comes from the DB.
- **CSS uses logical properties only**: Tailwind `ms-*`, `me-*`, `ps-*`, `pe-*`, `start-*`, `end-*`, `text-start`, `text-end`, `rounded-s-*`, `rounded-e-*`, `border-s`, `border-e`. Never `ml-`, `mr-`, `pl-`, `pr-`, `left-`, `right-`, `text-left`, `text-right` for layout.
- Images always rendered through Media Library conversions (WebP), with `loading="lazy"` except the hero image, and always with `alt` text.
- Eager-load relations on listing pages; no N+1 queries.
- Git: one commit per completed task with a clear message.

---

## 4. Roles and permissions

| Role | Access |
| --- | --- |
| `super_admin` | Everything, including users, roles, activity log |
| `admin` | Fleet, categories, bookings, content, settings, testimonials, FAQ, messages. No user management. |
| `booking_agent` | Bookings (view, edit status and notes) and contact messages. Read-only view of cars. |

- Generate Filament Shield policies for every resource.
- Seed one `super_admin` from env: `ADMIN_EMAIL`, `ADMIN_PASSWORD`.
- Log create/update/delete on Car, Booking (status changes), PageSection, settings with `spatie/laravel-activitylog`.

---

## 5. Multilingual-ready rules (Phase 1 builds these, Phase 2 just enables locales)

1. `config/app.php` locale `en`, fallback `en`. Create `config/locales.php`:
   ```php
   return [
       'supported' => ['en' => ['name' => 'English', 'dir' => 'ltr']],
       // later: 'ar' => ['name' => 'العربية', 'dir' => 'rtl'], 'ru' => ['name' => 'Русский', 'dir' => 'ltr'],
   ];
   ```
2. All public routes are prefixed with `{locale}` (constrained to supported keys). `/` redirects to `/en`. Middleware `SetLocale` sets `app()->setLocale()` from the route.
3. `<html lang="{{ app()->getLocale() }}" dir="{{ config('locales.supported.'.app()->getLocale().'.dir') }}">`.
4. Every customer-facing text column is a **JSON translatable column** using `HasTranslations` (marked **(T)** in section 6). In Phase 1 only the `en` key is filled.
5. Page section content (section 8.4) stores locale-keyed JSON and reads with a fallback to `en`.
6. Language switcher component exists in the header but renders nothing when only one locale is supported.
7. Fonts: use a Google font pair that supports Latin + Cyrillic; leave a clearly marked hook in the layout to load an Arabic font for `dir=rtl`.
8. Mail templates use `__()` keys and are sent in the locale the booking was made in (store `locale` on bookings).

---

## 6. Database schema

Add `created_at`/`updated_at` on all tables. **(T)** = JSON column, translatable.

### Fleet
- **categories**: id, name (T), slug (unique), description (T, nullable), sort_order (int, default 0), is_active (bool). Media collection `icon` (single).
- **brands**: id, name, slug (unique), sort_order, is_active. Media collection `logo` (single).
- **car_models**: id, brand_id (FK, cascade restrict), name, slug, is_active. Unique (brand_id, slug).
- **features**: id, name (T), icon (string, nullable: a heroicon or svg name), sort_order.
- **cars**: id, category_id, brand_id, car_model_id, name (T), slug (unique), year (smallint), transmission (enum), fuel_type (enum), seats, doors, luggage (nullable), engine (nullable string), colour (nullable), mileage_limit_per_day (nullable int), price_day (decimal 10,2), price_week (nullable), price_month (nullable), offer_price_day (nullable), deposit (nullable), short_description (T, nullable), description (T, nullable, rich text HTML), rental_terms (T, nullable), status (enum: active, inactive, maintenance), is_featured (bool), sort_order, meta_title (T, nullable), meta_description (T, nullable), softDeletes. Media: `main` (single), `gallery` (multiple, ordered, max 15). Conversions: `thumb` 400w, `card` 800w, `large` 1600w, all WebP.
- **car_feature**: car_id, feature_id (pivot).

Rule: a category, brand or model used by any car cannot be deleted (block in model `deleting` event and hide delete action in Filament); it can be deactivated.

### Booking
- **locations**: id, name (T), extra_charge (decimal, default 0), sort_order, is_active.
- **addons**: id, name (T), price (decimal), price_type (enum: per_day, per_booking), sort_order, is_active.
- **bookings**: id, reference (unique, format `BK-{YYYY}-{00001}`), car_id (FK, nullOnDelete), car_snapshot (JSON: name, brand, model, year, price_day at time of booking), pickup_at (datetime), return_at (datetime), pickup_location_id (nullable FK), pickup_location_text (nullable), dropoff_location_id (nullable FK), dropoff_location_text (nullable), customer_name, phone, email, notes (text, nullable), estimated_total (decimal), status (enum), internal_notes (text, nullable), locale (string 5), ip_address (nullable), user_agent (nullable).
- **booking_addon**: booking_id, addon_id, price (decimal snapshot).
- **booking_status_logs**: id, booking_id, from_status (nullable), to_status, user_id (nullable), note (nullable), created_at.

### Content
- **pages**: id, key (unique: home, about, services, faq, contact, fleet, booking, privacy, terms), title (T), meta_title (T, nullable), meta_description (T, nullable). Media `og_image` (single).
- **page_sections**: id, page_id, key (string, e.g. `hero`), type (string, maps to a Blade component), content (JSON, locale-keyed — see 8.4), sort_order, is_visible (bool). Media collections per section as needed (`images`, multiple). Unique (page_id, key).
- **services**: id, title (T), description (T), icon (nullable), sort_order, is_active. Media `image` (single).
- **testimonials**: id, customer_name, content (T), rating (tinyint 1–5), sort_order, is_active. Media `photo`.
- **faqs**: id, question (T), answer (T), group (T, nullable), sort_order, is_active.
- **contact_messages**: id, name, email, phone (nullable), message (text), is_read (bool), ip_address (nullable).
- **menu_items**: id, location (enum: header, footer), label (T), url (string; internal paths stored without locale, e.g. `/fleet`), sort_order, is_active, opens_new_tab (bool).

### Settings (spatie/laravel-settings classes)
- `GeneralSettings`: site_name, tagline, currency_code (default `AED`), currency_symbol, maintenance_banner (nullable). Logo light, logo dark, favicon stored via media on a singleton or as file paths.
- `ContactSettings`: phones (array), emails (array), address, map_embed_url, working_hours (array of {day, hours}).
- `WhatsAppSettings`: enabled (bool), numbers (array of {label, number}), template_general, template_car, template_booking.
- `SocialSettings`: facebook, instagram, tiktok, x, youtube, linkedin (all nullable URLs).
- `NotificationSettings`: booking_recipients (array of emails), send_customer_confirmation (bool).
- `IntegrationSettings`: ga4_id, gtm_id, meta_pixel_id, turnstile_site_key, turnstile_secret_key (all nullable).
- `BookingSettings`: show_estimated_price (bool), min_rental_days (int, default 1), terms_page_url.

---

## 7. Public website

### 7.1 Routes (all under `/{locale}`)
| Route | Page |
| --- | --- |
| `/` | Home |
| `/about` | About Us |
| `/services` | Services |
| `/faq` | FAQ / Rental Terms |
| `/contact` | Contact (GET + POST) |
| `/fleet` | Fleet listing (filters via query string) |
| `/fleet/category/{slug}` | Fleet filtered by category (canonical SEO URL) |
| `/cars/{slug}` | Car detail |
| `/booking` | Booking form (`?car={slug}` pre-selects) |
| `/booking/thank-you/{reference}` | Success page (signed URL) |
| `/privacy`, `/terms` | Legal pages (rich text from CMS) |

Plus `/sitemap.xml`, `/robots.txt`, custom 404 and 500 pages.

### 7.2 Global layout
- **Header**: logo, menu from `menu_items` (header), phone, "Book Now" button, language switcher slot. Sticky, shrinks on scroll. Mobile: hamburger with slide-in drawer (Alpine).
- **Footer**: logo, tagline, footer menu, contact details, working hours, social icons, copyright with current year.
- **Floating WhatsApp button** bottom-end on all pages (section 9).
- Cookie consent banner (text from `__()`), analytics scripts only load after consent.
- SEO in `<head>`: title, meta description, canonical, Open Graph + Twitter tags, JSON-LD `AutoRental` / `LocalBusiness` on home, `Product`-style JSON-LD on car detail.

### 7.3 Home page sections (from `page_sections`, ordered and toggleable)
1. `hero` — slider (1–5 slides): image, heading, subheading, primary button (label + link), secondary button (WhatsApp). Swipeable, autoplay with pause, single slide shows static.
2. `quick_search` — category + brand selects → submits to `/fleet`.
3. `featured_cars` — heading + subheading; shows up to 8 cars where `is_featured`.
4. `categories` — heading; tiles for active categories.
5. `why_us` — heading; repeater of 3–6 items {icon, title, text}.
6. `how_it_works` — heading; repeater of 3–4 steps {title, text}.
7. `brands` — heading; strip of active brand logos.
8. `testimonials` — heading; carousel of active testimonials.
9. `cta` — heading, text, background image, buttons.

### 7.4 Inner pages
- **About**: `intro` (heading, rich text, image), `stats` (repeater {number, label}), `mission` (heading, text), `gallery` (images).
- **Services**: `intro` + grid of `services` records.
- **FAQ**: `intro` + accordion of `faqs` grouped by `group`; `terms` rich text section.
- **Contact**: `intro`, contact details from settings, map embed, working hours, contact form (name, email, phone, message, honeypot, Turnstile). Stores to `contact_messages`, emails `booking_recipients`, rate limit 5/hour/IP.

### 7.5 Car card component (reused everywhere)
Image (`card` conversion), name, category, year, price per day (show offer price with strikethrough original if set), 3–4 key specs with icons (transmission, seats, fuel, luggage), buttons: "Details", "Book", WhatsApp icon.

### 7.6 Fleet listing
- Active cars only, 12 per page.
- Filters: category, brand, model (filtered by brand), transmission, seats (min), fuel type, price range (min/max per day). Sort: price asc, price desc, newest, featured first (default).
- Filters live in the query string; filter UI is a sidebar on desktop and a bottom sheet/drawer on mobile.
- Empty state with a "Clear filters" link and WhatsApp CTA.

### 7.7 Car detail
- Gallery with thumbnails and lightbox (Alpine, keyboard + swipe).
- Name, brand, model, year, category badge, prices (day/week/month, deposit), specs table, features list, description (rich text), rental terms.
- Sticky booking box on desktop (price + "Book this car" + "Ask on WhatsApp"); fixed bottom bar on mobile.
- "Similar cars": up to 4 from the same category.
- 404 if car is inactive or soft-deleted. `maintenance` cars show the page with booking disabled and a notice.

---

## 8. Booking

### 8.1 Form fields and validation (`StoreBookingRequest`)
| Field | Rules |
| --- | --- |
| car_id | required, exists, car status active |
| pickup_at | required, date, after now |
| return_at | required, date, after pickup_at, at least `min_rental_days` |
| pickup_location_id or pickup_location_text | one required (dropdown with "Other" → text) |
| same_dropoff (bool) / dropoff_location_id / dropoff_location_text | required unless same_dropoff |
| customer_name | required, string, max 100 |
| phone | required, valid international format (store E.164-ish, keep country code) |
| email | required, email:rfc |
| addons[] | optional, exists in active addons |
| notes | nullable, max 1000 |
| terms | accepted |
| website (honeypot) | must be empty |
| turnstile token | verified if keys configured |

Rate limit: 5 submissions per IP per hour.

### 8.2 Estimated price (`EstimateBookingPrice` action, used by both frontend preview and server)
- days = ceil((return_at − pickup_at) in hours / 24), min 1.
- rate: if days ≥ 30 and price_month → price_month / 30 per day; elif days ≥ 7 and price_week → price_week / 7 per day; else offer_price_day ?? price_day.
- total = days × rate + addons (per_day × days, per_booking × 1) + location extra charges.
- Show live on the form via a small JSON endpoint `POST /{locale}/booking/estimate` (rate limited), labelled "Estimated total — final price confirmed by our team". Hidden when `show_estimated_price` is false.
- Server recalculates on submit; never trust client totals.

### 8.3 On submit (`CreateBooking` action, in a DB transaction)
1. Generate unique reference.
2. Save booking with car snapshot, addons with price snapshot, locale, IP, user agent.
3. Log initial status `new` in `booking_status_logs`.
4. Queue `NewBookingAdminMail` to `booking_recipients` (all details + link to the booking in admin).
5. Queue `BookingReceivedCustomerMail` if `send_customer_confirmation`.
6. Redirect to signed thank-you URL showing reference, summary, and a WhatsApp button pre-filled with `template_booking`.

### 8.4 Statuses
`new` → `contacted` → `confirmed` → `completed`; `cancelled` and `rejected` allowed from any non-completed status. Every change writes `booking_status_logs` (with admin user) and may send `BookingStatusChangedMail` to the customer (admin chooses via a toggle on the action).

No availability blocking in this version. Keep the code structured so an `AvailabilityChecker` can be added later.

---

## 9. WhatsApp integration (click-to-chat, no API)

- Helper `WhatsApp::link(string $template, array $vars = [], ?string $number = null): string` → `https://wa.me/{digits}?text={urlencoded}`.
- Templates from `WhatsAppSettings` with placeholders `{car}`, `{year}`, `{reference}`, `{url}`.
- Placements: floating button (all pages; if multiple numbers, open a small chooser), car card icon, car detail, booking success page, contact page, CTA sections.
- Hide all WhatsApp UI when `enabled` is false or no number is set.
- Push a `whatsapp_click` event to `dataLayer` on click if analytics is enabled.

---

## 10. Admin panel (Filament, `/admin`)

Brand the panel with the site logo and primary colour from settings. Navigation groups: **Dashboard**, **Fleet**, **Bookings**, **Website Content**, **Settings**, **Users & Access**.

### 10.1 Dashboard widgets
- Stats: new bookings today, new bookings this week, bookings this month, active cars, unread messages.
- Chart: bookings per day, last 30 days.
- Table: latest 10 bookings with inline status change.
- Top 5 requested cars (last 30 days).

### 10.2 Fleet group
- **CarResource**: table (main image thumb, name, brand, model, category, price/day, status badge, featured toggle column), search, filters (category, brand, status, featured, trashed), bulk activate/deactivate/delete, reorder by `sort_order`, `Replicate` action ("Duplicate car"). Form in tabs: Basic info · Pricing · Specs · Features · Media · Content · SEO. Model select depends on brand (reactive). Slug auto from name, editable. Gallery: drag-and-drop, reorder, 5 MB max, images only.
- **CategoryResource**, **BrandResource** (with nested **CarModel** relation manager), **FeatureResource**: simple CRUD with reorder, active toggles, delete blocked when in use.

### 10.3 Bookings group
- **BookingResource**: table (reference, customer, phone, car, pickup, return, estimated total, status badge, created). Filters: status, car, date range. Search: reference, name, phone, email. Navigation badge = count of `new`.
- View page: all details, addons, internal notes (editable), status history timeline, header actions: Change status (with "notify customer" toggle), Open WhatsApp chat, Call, Email.
- Export to Excel/CSV (Filament export action).
- **LocationResource**, **AddonResource**: CRUD with reorder and active toggle.
- **ContactMessageResource**: list, mark read/unread, view, reply via `mailto:`. Badge = unread count.

### 10.4 Website Content group
- **Pages** (custom resource over `pages`): per page edit title + SEO fields + OG image, and a relation manager / builder for its `page_sections`.
- **Section editing**: each section `type` has its own Filament form schema defined in one place (`app/Content/SectionTypes/*`), e.g. `HeroSection::form()`, `HeroSection::defaults()`. Repeaters for slides/items. Toggle `is_visible`, reorder by `sort_order`. Content saved under `content[app()->getLocale()]`.
- Read helper on `PageSection`: `$section->get('heading')` returns `content[current locale][heading]` falling back to `content['en'][heading]`.
- **ServiceResource**, **TestimonialResource**, **FaqResource**, **MenuItemResource**: CRUD with reorder and active toggles.
- Rich text fields use Filament's rich editor (bold, italic, lists, links, h2/h3 only).

### 10.5 Settings group
One Filament settings page per settings class in section 6 (General, Contact, WhatsApp, Social, Notifications, Integrations, Booking). Settings changes clear the relevant cache.

### 10.6 Users & Access (super_admin only)
- **UserResource**: CRUD, role select, active toggle (inactive users cannot log in).
- Shield roles page.
- Activity log viewer (read-only table).
- Profile page for all admins (name, email, password).

Admin login rate limited; password reset via email.

---

## 11. Content seeding

Create seeders that make the site look complete on first run:
- Pages + all sections with realistic placeholder English content and placeholder images (generate simple local placeholder images; do not hotlink external images).
- 6 categories (Economy, Sedan, SUV, Luxury, Sports, Van), 8 brands with models, 12 features, 12 demo cars with images, 4 services, 6 testimonials, 10 FAQs, 4 locations (Airport, Office, Hotel delivery, Other), 4 addons (Child seat, GPS, Additional driver, Chauffeur), header/footer menus, default settings.
- Keep demo data in `DemoSeeder` separate from `EssentialSeeder` (pages, sections, settings, roles, admin user) so production can run only essentials.

---

## 12. Non-functional requirements

- **Responsive**: verify at 360, 390, 768, 1024, 1280, 1440 px. No horizontal scroll anywhere. Tap targets ≥ 44 px.
- **Performance**: PageSpeed mobile ≥ 80 on Home and Fleet. WebP images with `srcset`, lazy loading, Vite production build, cache settings and menus (`Cache::rememberForever` with tag/keys cleared on save), `php artisan optimize` on deploy.
- **SEO**: unique titles/descriptions per page and car (fallback to generated ones), canonical URLs, sitemap including cars and categories, robots.txt from config, alt text everywhere, breadcrumb JSON-LD on car pages.
- **Security**: HTTPS, CSRF, Form Requests, rate limiting, upload MIME/size validation, `APP_DEBUG=false` in production, security headers middleware (X-Frame-Options, X-Content-Type-Options, Referrer-Policy, basic CSP that allows the configured analytics and Turnstile).
- **Accessibility**: semantic landmarks, labelled inputs, visible focus states, keyboard-operable menus, sliders, accordions and lightbox, WCAG AA contrast.
- **Queues**: database driver; mails are queued.
- **Backups**: `spatie/laravel-backup` daily DB + `storage/app/public`, keep 14 days.

---

## 13. Tests (Pest)

Minimum feature tests:
- Home, about, services, faq, contact, fleet, car detail return 200 with seeded data; inactive car returns 404.
- `/` redirects to `/en`; unsupported locale returns 404.
- Fleet filters and sorting return the right cars.
- Booking: valid submission creates booking, addons, status log, queues both mails; invalid dates rejected; honeypot rejects; rate limit hits after 5.
- Estimate action: day/week/month rate selection and addon math.
- Contact form stores message and queues mail.
- Admin: guests redirected to login; `booking_agent` cannot access Car create or Users; `admin` cannot access Users.
- Deleting a category in use is blocked.
- WhatsApp link helper encodes templates correctly.

All tests must pass before each milestone is marked complete.

---

## 14. Milestones (do them in order)

1. **Setup** — Laravel, Filament, packages, Tailwind/Vite, Pest, locale routing + middleware, base layout, roles, admin user seeder. Check: `/en` renders, `/admin` login works.
2. **Fleet backend** — migrations, models, enums, Filament resources for categories, brands, models, features, cars, media conversions, delete-protection rules, demo seed. Check: create a car with gallery in admin.
3. **Content system** — pages, page sections, section type registry, settings classes and pages, services, testimonials, FAQs, menus, essential + demo seeders. Check: editing hero heading in admin changes the homepage.
4. **Public pages** — layout, header, footer, home sections, about, services, FAQ, contact form, legal pages, 404. Check: all pages render responsively from DB content.
5. **Fleet frontend** — car card, fleet listing with filters/sort/pagination, category URLs, car detail with gallery and similar cars.
6. **Booking** — form, estimate endpoint, validation, create action, mails, thank-you page, booking admin with statuses, history, export, locations, addons, contact messages admin.
7. **WhatsApp + dashboard** — helper, all placements, chooser, dashboard widgets, navigation badges.
8. **SEO, performance, security, a11y** — meta, JSON-LD, sitemap, robots, headers, caching, image `srcset`, cookie consent, analytics loading.
9. **Tests and QA** — complete section 13, fix all failures, run responsive checks at all listed widths.
10. **Deployment prep** — `README.md` (local setup, env vars, deploy steps for VPS and cPanel, cron line `* * * * * php artisan schedule:run`, queue via `schedule:run` + `queue:work --stop-when-empty` for cPanel), `.env.example` complete, `DEPLOY.md` checklist, production seeder command.

After each milestone: run `php artisan test`, `npm run build`, and report what was done and anything that needs a human decision.

---

## 15. Out of scope (do not build)

Online payments, availability calendar / double-booking prevention, customer accounts, WhatsApp Business API automation, mobile apps, additional languages (only the readiness in section 5), blog.

---

## 16. Open items (use sensible defaults, make them configurable, list them in the README)

- Exact inner pages (default: About, Services, FAQ/Terms, Contact).
- Currency (default AED, configurable in settings).
- Default phone country code for the phone input (configurable).
- Final brand colours and logo (use placeholders driven by CSS variables in `resources/css/app.css` so a rebrand is one file).
