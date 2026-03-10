# Improvements & New Features Plan

Based on a full read of the project (Laravel 12, Inertia + Vue 3, ride-hailing/booking, admin/owner/driver/dispatcher/agent/franchise/user, payments, zones, promo, support, chat, reports), here is a structured plan.

---

## 1. Code Quality & Maintainability

### 1.1 Validation
- **Current:** Form Requests are used in only a few places (e.g. `CreateTripRequest`); most validation is inline in controllers.
- **Improve:** Introduce Form Requests for all API and web actions (create/update trip, user, driver, promo, settings, etc.) to centralize rules and keep controllers thin.
- **Benefit:** Consistent validation, easier testing, clearer API contracts.

### 1.2 TODOs in codebase
- **Current:** Multiple `@TODO` comments for: “send SMS & email to user”, “validate if user has any trip with same time period”, “adhoc user transformer”, “check if rental is enabled”.
- **Improve:** Turn these into tracked tasks; implement at least: (1) duplicate/same-time trip validation, (2) optional SMS/email notifications for trip lifecycle (or document why deferred).

### 1.3 Typo and consistency
- **Current:** `firstorNew([])` / `firstornew([])` in `AppServiceProvider` (PHP is case-insensitive; standard is `firstOrNew`).
- **Improve:** Use `firstOrNew([])` consistently and add null checks when using `->value` (e.g. `$admin_url->value ?? 'admin'`) to avoid errors when the setting row does not exist.

### 1.4 Bootstrap and settings
- **Current:** `AppServiceProvider::boot()` uses `Schema::hasTable('settings')` and runs many `Setting::whereName(...)->firstOrNew([])` and `view()->share()` on every request.
- **Improve:** Cache settings (e.g. Redis or `Cache::remember`) with a key like `app.settings` and invalidate on setting update. Move view-sharing to a dedicated middleware or Inertia shared data so boot stays light and install/migrate without `settings` table does not break.

### 1.5 Exception handling
- **Current:** Exception handler has optional “send exception to email” logic; the actual send (e.g. `SendExceptionToEmailNotification`) is commented out.
- **Improve:** Either enable and configure exception notification (email or Sentry/Bugsnag) or remove dead code. Prefer a dedicated error-tracking service for production.

---

## 2. Testing

### 2.1 Current state
- Only default `ExampleTest` (feature + unit); no tests for auth, trips, payments, or permissions.
- **Improve:** Add:
  - **Feature:** Auth (login/register per user type), create/cancel trip (API), key API endpoints (profile, request history, driver accept/reject).
  - **Unit:** Critical helpers (e.g. ETA, pricing, promo application), Form Request rules.
  - **Integration (optional):** One payment gateway (e.g. Stripe) with mocked HTTP.
- Use `RefreshDatabase` (or a test DB) and factories for `User`, `Request`, `Driver`, `Zone`, etc.

### 2.2 Continuous integration
- Add a CI workflow (GitHub Actions or similar) that runs `composer install`, `npm ci && npm run build`, `php artisan test`, and optionally `php artisan migrate --force` on a test database.

---

## 3. Security & Resilience

### 3.1 Login rate limiting
- **Current:** `FortifyServiceProvider` has `RateLimiter::for('login', ...)` commented out.
- **Improve:** Enable login rate limiting (e.g. 5 attempts per minute per IP or per email) to reduce brute-force risk.

### 3.2 API throttling
- **Current:** Global API throttle (e.g. 60/min) and per-route throttles (e.g. 120, 30) in `bootstrap/app.php` and route files.
- **Improve:** Keep throttling; consider stricter limits for auth-related and payment endpoints; document limits in API docs.

### 3.3 Authorization
- **Current:** Permissions and roles (e.g. `RouteNeedsPermission`, `access()->hasRole()`); many permission constants in `App\Base\Constants\Auth\Permission`.
- **Improve:** Consistently use policy or `authorize()` in controllers for resources (trip, driver, owner, promo, etc.) so forbidden access returns 403 with a clear message.

---

## 4. Performance

### 4.1 N+1 and queries
- **Current:** Controllers use many direct `->get()`, `->first()`, and relationship access without eager loading.
- **Improve:** Use `->with()` / `->load()` for relations (e.g. request with user, driver, zone_type). Run Laravel Debugbar or Telescope in dev and fix N+1 on list/detail endpoints (dashboard, request history, driver/owner lists).

### 4.2 Caching
- **Current:** Some helpers use `Cache::get()` (e.g. currency); settings and third-party config are not cached.
- **Improve:** Cache settings and non-secret third-party config (e.g. feature flags, map keys) with TTL or event-based invalidation. Cache heavy read-only or slow-changing data (e.g. zone list, cancellation reasons) where appropriate.

### 4.3 Queues
- **Current:** Jobs exist (e.g. push notifications, MQTT, socket, mail, exception email); usage is mixed.
- **Improve:** Ensure all “fire-and-forget” notifications (SMS, email, push) and non-critical side effects are dispatched to queues; run a queue worker in production and document it in README/deploy docs.

---

## 5. API & Documentation

### 5.1 API versioning
- **Current:** Routes under `api/v1`; no formal versioning strategy.
- **Improve:** Document that v1 is current; plan for v2 (e.g. new URL prefix or header) when making breaking changes. Keep v1 stable or clearly deprecated.

### 5.2 API documentation
- **Current:** Some `@group` and `@bodyParam` in controller docblocks; no single source of truth for all endpoints.
- **Improve:** Add OpenAPI (Swagger) spec generation (e.g. L5-Swagger or Scramble) and serve UI at e.g. `/api/docs`. Document auth (Sanctum), throttling, and error format.

### 5.3 Response format
- **Current:** Fractal transformers used widely; response shape may vary by endpoint.
- **Improve:** Standardize JSON shape (e.g. `{ data, message, errors }`) for success and validation/error responses; document in API docs.

---

## 6. Frontend (Vue 3 / Inertia)

### 6.1 State and data
- **Current:** Vuex for permissions, layout, notifications, todo; Ziggy for `route()` (recently fixed).
- **Improve:** Ensure Ziggy/`route()` is used consistently; consider Pinia for new modules if you prefer Composition API style. Keep permissions and shared app state in one place.

### 6.2 Composition API vs Options API
- **Current:** Mix of Options API (e.g. `booking.vue`) and Composition API / `<script setup>` in other components.
- **Improve:** Prefer Composition API for new components; migrate high-traffic or complex pages when touching them. Standardize on one style per module.

### 6.3 i18n and accessibility
- **Current:** vue-i18n and `$t()` used; direction and locale from backend.
- **Improve:** Audit missing translation keys; add `aria-*` and roles where needed for critical flows (login, booking, payment).
- **Done:** Accessibility added to admin Login, customer login (user-web index), and booking form (ride_info, pickup/drop, errors with `role="alert"`). i18n keys for these flows exist in `public/lang/*/view_pages_*.json` and `user_app.json`. See `docs/FRONTEND_CONVENTIONS.md` for ongoing audit and a11y guidelines.

---

## 7. Database & Migrations

### 7.1 Migration count
- **Current:** 400+ migrations; many small, incremental changes.
- **Improve:** For new work, prefer fewer, descriptive migrations. Consider squashing old migrations only for new installs (document process) so existing deployments are unaffected.

### 7.2 Indexes and queries
- **Improve:** Add indexes for columns used in `WHERE`, `ORDER BY`, and joins (e.g. `requests.user_id`, `requests.driver_id`, `requests.created_at`, `drivers.zone_id`, `promo_users.promo_id`). Review slow-query log and add indexes based on real usage.

---

## 8. DevOps & Environment

### 8.1 README and docs
- **Current:** README is generic Laravel text.
- **Improve:** Add project-specific README: what the app does (admin panel, mobile API, web booking), PHP/Node versions, env vars (with `.env.example`), how to run migrations, queues, scheduler, and (if any) install wizard. Link to LaRecipe or internal docs for business rules.
- **Done:** [README.md](README.md) describes the app, requirements, quick start, queues, scheduler, and links to `.env.example`, [docs/SCHEDULER_AND_CRON.md](docs/SCHEDULER_AND_CRON.md), [docs/FRONTEND_CONVENTIONS.md](docs/FRONTEND_CONVENTIONS.md), and the improvement plan.

### 8.2 .env.example
- **Improve:** Keep `.env.example` complete with every key the app uses (no secrets), with placeholder values and short comments (e.g. Stripe, Firebase, map keys, SMS, mail, queue driver).
- **Done:** [.env.example](.env.example) includes app, DB, session, cache, queue, filesystem, Redis, mail, logging, broadcast, AWS, payment (Stripe, PayPal), Firebase, FCM, reCAPTCHA, SMS, Sanctum, Scribe, web/SEO, and Vite keys with short comments.

### 8.3 Scheduler and cron
- **Improve:** Document required cron entry for `schedule:run` and list commands (e.g. cancel expired requests, assign scheduled rides, subscription expiry, document expiry notifications). Ensure these are registered in `routes/console.php` or `Kernel`.
- **Done:** Schedule is defined in `app/Console/Kernel.php`. [docs/SCHEDULER_AND_CRON.md](docs/SCHEDULER_AND_CRON.md) documents the required cron entry and lists all scheduled commands (cancel request, assign drivers, document expiry, subscription expiry, OTP clear, promo deactivate, etc.).

---

## 9. New Features (Prioritized)

### 9.1 High value (align with existing product)
- **Trip lifecycle notifications:** Optional SMS/email at key steps (request created, driver assigned, trip started, completed, cancelled) with templates and feature flags.
- **Duplicate / overlapping trip check:** Before creating a request, validate that the user has no other active or overlapping scheduled trip (reuse logic across API, web booking, dispatcher).
- **Driver availability windows:** Let drivers set “available from–to” (e.g. time windows) and filter in driver search / assignment.
- **Improved dashboard analytics:** More charts (trips per day, revenue per zone, cancellation reasons) using existing AmCharts/FullCalendar; cache heavy aggregations.

### 9.2 Medium value
- **Driver Dashboard (under Driver Management):** Dedicated analytics page for driver records: summary cards (total / approved / pending / active), charts (drivers per zone, drivers by vehicle type, registrations over time, acceptance ratio distribution, top drivers by trips/earnings), optional service-location filter. See [docs/DRIVER_DASHBOARD_FEATURE.md](docs/DRIVER_DASHBOARD_FEATURE.md) for the full spec.
- **In-app chat enhancements:** Typing indicator, read receipts, optional file/image upload (if not already present).
- **Promo improvements:** “First ride free” (one-time per user), minimum trip amount, and usage cap per user; expose in API and admin.
- **Export/reports:** Scheduled report emails (e.g. daily trip summary) and export (Excel/PDF) for reports already on screen.
- **Web booking – save card:** Optional “save card for next time” for card payments on web booking (Stripe Customer / PaymentMethod), with PCI-safe handling.

### 9.3 Nice to have
- **PWA for admin or web booking:** Service worker + manifest for “Add to Home Screen” and offline fallback.
- **Dark mode:** Toggle for admin/panel (store preference in user or localStorage) and respect system preference. **Done:** Theme in `localStorage.theme`; fallback to `prefers-color-scheme`; inline script for first-paint; all nav-bars apply and listen for system changes when no override.
- **Audit log:** Log critical actions (user/driver/owner create/update, trip create/cancel, payment, role change) in an `activity_log` table for support and compliance.
- **Multi-language admin:** Use existing languages/translations for admin UI (not only landing/mobile) so support and ops can use preferred language.

---

## 10. Implementation Priority (Suggested)

| Phase | Focus |
|-------|--------|
| **Phase 1 (Quick wins)** | Fix `firstOrNew` and null-safe setting access; enable login rate limiting; add project README and .env.example; cache settings in boot. |
| **Phase 2 (Stability)** | Form Requests for main flows; implement duplicate/same-time trip validation; ensure notifications (SMS/email/push) are queued; fix N+1 on top 5 list/detail endpoints. |
| **Phase 3 (Quality)** | Feature tests for auth and create/cancel trip; API docs (OpenAPI); exception tracking (e.g. Sentry). |
| **Phase 4 (Features)** | Trip lifecycle notifications; driver availability windows; dashboard analytics improvements; audit log. |

---

This plan is a living document: adjust priorities and scope to match your roadmap and resources.
