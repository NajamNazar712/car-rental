# Car Rental Website + Admin Panel

Laravel 13 + Filament 5 car rental site. The full requirements are in [car-rental-build-spec.md](car-rental-build-spec.md).

**Status:** Milestones 1–3 are done (setup, fleet backend, content system). The public pages (Milestone 4 onward) are not built yet.

## Stack

| Layer | Version / package |
| --- | --- |
| PHP | 8.3+ (Composer platform pinned to 8.3.0 so the lockfile works on 8.3 hosts) |
| Laravel | 13.x |
| Admin | Filament 5.10 at `/admin` |
| Frontend | Blade + Tailwind CSS 4 (Vite) + Alpine.js |
| Media | spatie/laravel-medialibrary 11 + Filament media plugin |
| Settings | spatie/laravel-settings 3 + Filament settings plugin |
| Translations | spatie/laravel-translatable 6 (no Filament plugin, see below) |
| Roles | spatie/laravel-permission 8 + bezhansalleh/filament-shield 4 |
| Other | spatie activitylog 4, sitemap 8, backup 10 |

## Local setup (Laragon / Windows)

```bash
composer install
npm install
cp .env.example .env        # then edit DB_* and ADMIN_* values
php artisan key:generate
php artisan storage:link
php artisan migrate --seed  # essentials + demo data (demo data is skipped when APP_ENV=production)
npm run build               # or: npm run dev
```

- Database: MySQL `car_rental`, user `root`, no password (Laragon default).
- Open `APP_URL` (e.g. `http://rental-service.test` after clicking Laragon → Reload, or `php artisan serve`).
- `APP_URL` must match the address you browse with, or uploaded images will not load.
- Admin: `/admin`. The super admin comes from `ADMIN_EMAIL` / `ADMIN_PASSWORD` in `.env`.
- Demo staff account (from `StaffDemoSeeder`, not created in production):
  - `manager@carrental.test` / `Manager@12345` has the `admin` role.
  - It only sees what the `admin` role has been granted in Users & Access → Roles.

### Seeders

| Seeder | Contents | Production |
| --- | --- | --- |
| `EssentialSeeder` | Roles and permissions, super admin, CMS pages and sections, header/footer menus | Yes: `php artisan db:seed --class=EssentialSeeder --force` |
| `DemoSeeder` | 6 categories, 8 brands with models, 12 features, 12 cars with images, 4 services, 6 testimonials, 10 FAQs, demo staff | No |

Default settings are created by the settings migration in `database/settings/`, not by a seeder.

Seeders are idempotent: existing pages, sections and menus are not overwritten, so admin edits survive re-seeding. Placeholder images are generated locally with GD; nothing is hotlinked.

## Content model

- **Translatable columns** are JSON (`{"en": "..."}`) via `spatie/laravel-translatable`.
  - `App\Models\Concerns\HasLocalizedTranslations` makes `toArray()` return the current-locale value.
  - That is what lets Filament forms edit translations per locale without a translatable plugin.
- **Page sections**:
  - `page_sections.content` is locale-keyed JSON.
  - Read a value with `$section->get('heading')`, which falls back to `en`.
  - Admin forms edit `localized_content`, which reads and writes `content[app()->getLocale()]`.
- **Section types**:
  - Each type's admin form lives in `app/Content/SectionTypes/*`.
  - `app/Content/SectionTypeRegistry.php` maps each `type` to its form class.
  - The public view for a type is `resources/views/components/sections/{type}.blade.php` (`_` becomes `-`). Types without a view yet render a generic fallback.
- **Section images** use one ordered `images` media collection per section:
  - Hero: image *n* belongs to slide *n*.
  - CTA and intro: the first image.
  - Gallery: all images.
- **Pages and sections are fixed.** Admins edit content, visibility and order but cannot create or delete them, because each section type needs matching Blade code.

## Roles

| Role | Access |
| --- | --- |
| `super_admin` | Vendor/maintenance tier. Everything, including the super-admin-only screens below (bypasses permission checks through Shield). Hidden from client admins. |
| `admin` | Client admin. Has whatever permissions a super admin grants it in the Roles screen. Cannot see super admins or the `super_admin` role, and cannot edit its own `admin` role. |
| Other roles | Created in the Roles screen (e.g. a booking agent role). Any role grants panel access; what the user sees depends only on the role's permissions. |

Only the built-in roles (`super_admin`, `admin`) always exist and cannot be deleted.

### Super-admin-only screens

The website content is static for the client, so these screens are kept in code (with their routes, models,
tables and data) but are only available to super admins:

- Website Content: Pages (with sections), Services, Testimonials, FAQs, Menus
- Settings: Notifications, Integrations, Booking

How it works:

- Each class uses `App\Filament\Concerns\SuperAdminOnly`, which hides it from the menu and blocks URL access for everyone else.
- They are listed in the `exclude` arrays of `config/filament-shield.php`, so no permissions exist for them. They never appear in the role editor and cannot be granted to any role.
- `RoleSeeder` deletes any leftover permissions for excluded screens.

To offer one to the client again:

1. Remove the trait from its class.
2. Remove its entry from the Shield `exclude` arrays.
3. Re-run the role seeder.
4. Grant the new permissions to the roles that need them in Users & Access → Roles.

### Role seeder

`RoleSeeder` never assigns permissions to any role. It only:

- creates permissions for every admin screen;
- deletes permissions of super-admin-only screens;
- makes sure the built-in roles exist.

Re-run it after adding or re-enabling admin screens, then grant permissions manually in the Roles screen:

```bash
php artisan db:seed --class=RoleSeeder --force
```

> **Fresh install:** the `admin` role starts with no permissions. Log in as super admin and grant them in the Roles screen before handing over.

## Decisions and defaults

- **Filament translatable plugin is not installed.**
  - `filament/spatie-laravel-translatable-plugin` stops at Filament v3 and cannot be installed with Filament 5.
  - Agreed approach: keep the JSON translatable columns and add a translatable plugin (e.g. `lara-zeus/spatie-translatable`) in Phase 2.
- **activitylog is v4.12, not v5.** v5 requires PHP 8.4; v4.12 supports Laravel 13 on PHP 8.3.
- **Pest and automated tests were skipped** at the owner's request. Milestones were verified with smoke checks instead.
- **Laravel Boost** (suggested by the skeleton's `CLAUDE.md` / `AGENTS.md`) was not installed because it is not in the spec.
- **Currency:** AED, editable in Admin → Settings → General.
- **Default phone country code:** `+971`, editable in Settings → General.
- **Brand colours:** CSS variables at the top of `resources/css/app.css`. The admin primary colour is in Settings → General.
- **Fonts:** Inter + Manrope from Google Fonts (Latin + Cyrillic). The RTL font hook is marked in `resources/views/layouts/app.blade.php` and `app.css`.
- **Logos and favicon:** stored as file paths (public disk, `branding/`) on `GeneralSettings`.
- **Turnstile secret key:** stored encrypted in settings.
- **Settings:** cached (`SETTINGS_CACHE_ENABLED=true`), refreshed automatically on save, and every save is written to the activity log (`log_name = settings`).
- **Menus:** cached forever and cleared whenever a menu item is saved or deleted.
- **Media conversions:** queued (`QUEUE_CONVERSIONS_BY_DEFAULT`), so run `php artisan queue:work`. Without a worker, uploaded images show originals until the conversions are processed. Seeders generate conversions synchronously.
- **Cars:** Duplicate creates an inactive copy with a `-copy-xxxx` slug, plus the same features and images.
- **Lookup records:** a category, brand or model used by any car (including trashed cars) cannot be deleted. Bulk delete skips records that are in use.

## Open items

- Exact inner pages: About, Services, FAQ/Terms and Contact are seeded. Add or remove menu entries in Admin → Website Content → Menus.
- Final brand colours, logo and favicon.
- Real contact details, WhatsApp numbers and notification recipients (placeholders are seeded).
- Legal text for the Privacy and Terms pages (placeholders are seeded).
