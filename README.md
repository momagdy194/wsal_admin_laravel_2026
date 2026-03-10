# Ride-Hailing / Transport Admin & Web Booking

Laravel application providing an **admin panel**, **mobile API**, and **customer web booking** for a transport/ride-hailing product. Roles include admin, owner, dispatcher, agent, franchise, and driver; features include zones, pricing, promos, payments (Stripe, PayPal, Razorpay, Cashfree, Mercado Pago), support tickets, and optional Firebase (push, OTP, real-time).

## What this app does

- **Admin panel (Inertia + Vue 3):** Dashboard, users, drivers, owners, fleet, zones, map settings, ride requests, ongoing/scheduled rides, payments, promos, subscriptions, support, notifications, and general/third-party settings.
- **Mobile API:** REST API for user and driver apps (auth, trip lifecycle, payments, etc.).
- **Web booking:** Customer-facing flow to create a booking (instant or scheduled), with login (OTP/password), history, and profile.
- **Install wizard:** Optional first-run setup at `/` (when install routes are active) for verification and initial configuration.

## Requirements

- **PHP** 8.2+ (Laravel 12)
- **Node.js** 18+ (Vite, Vue 3, frontend build)
- **Database:** MySQL/MariaDB (or SQLite for local dev)
- **Composer** and **npm** / **yarn**

## Quick start

1. **Clone and install**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

2. **Configure environment**  
   Edit `.env`: set `APP_URL`, database (`DB_*`), and any optional services (mail, queue, Redis, Stripe, Firebase, SMS, etc.). See [.env.example](.env.example) for all keys and short comments.

3. **Database**
   ```bash
   php artisan migrate
   # Optional: php artisan db:seed
   ```

4. **Frontend**
   ```bash
   npm install
   npm run build
   # or for dev: npm run dev
   ```

5. **Run**
   ```bash
   php artisan serve
   ```
   Visit `APP_URL` (e.g. `http://localhost:8000`). If install wizard is enabled, go to `/`; otherwise use your configured login URLs (e.g. `/login/user` for customer, `/login/admin` for admin).

## Queues and scheduler

- **Queues:** Notifications, jobs, and heavy work should use a queue. Set `QUEUE_CONNECTION=database` (or `redis`) and run:
  ```bash
  php artisan queue:work
  ```

- **Scheduler:** The app relies on scheduled commands for cancelling expired requests, assigning drivers for regular/scheduled rides, document expiry notifications, subscription expiry, OTP cleanup, and promo deactivation. **You must add a cron entry** so Laravel’s scheduler runs every minute:
  ```bash
  * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
  ```
  See [docs/SCHEDULER_AND_CRON.md](docs/SCHEDULER_AND_CRON.md) for the full list of commands and schedule.

## Env and docs

- **[.env.example](.env.example)** – All environment keys the app uses, with placeholder values and short comments (no secrets).
- **[docs/SCHEDULER_AND_CRON.md](docs/SCHEDULER_AND_CRON.md)** – Cron setup and list of scheduled commands.
- **[docs/FRONTEND_CONVENTIONS.md](docs/FRONTEND_CONVENTIONS.md)** – Vue 3 / Inertia conventions (Composition API, state, i18n, accessibility).
- **[IMPROVEMENTS_AND_FEATURES_PLAN.md](IMPROVEMENTS_AND_FEATURES_PLAN.md)** – Product/tech improvement plan and phased priorities.

For business rules and internal processes, see LaRecipe or your internal docs if configured.

## License

Same as the base Laravel framework (MIT unless otherwise stated).
