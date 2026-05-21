# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

Laravel 13 + Livewire 3 + Volt + Alpine + Tailwind 3 + Chart.js. PHP 8.3+ (CI runs 8.4). MySQL 8.4 in production; SQLite for tests and E2E. Auth: TOTP 2FA (pragmarx/google2fa) + WebAuthn passkeys (laragear/webauthn). Excel via maatwebsite, PDFs via barryvdh/laravel-dompdf.

## Commands

| Task | Command |
|---|---|
| Boot full dev (server + queue + `pail` logs + vite) | `composer dev` |
| PHPUnit (in-memory SQLite, BCRYPT_ROUNDS=4) | `php artisan test` or `composer test` |
| Single test class / method | `php artisan test --filter=ProjectTest` / `--filter="ClassName::method_name"` |
| Bump memory if `test` OOMs (common) | `php -d memory_limit=1G artisan test` |
| Format | `./vendor/bin/pint` |
| Dark-mode lint (Blade) | `php scripts/lint-dark-mode.php` |
| E2E (Playwright, chromium light + dark + mobile) | `npm run e2e` / `npm run e2e:headed` |
| Reset E2E sqlite to seeded state | `npm run e2e:reset` |
| Vite | `npm run dev` / `npm run build` |
| Create an admin out-of-band | `php artisan app:create-admin` |
| Trigger scheduled tasks manually | `php artisan projects:send-alerts` / `projects:weekly-report` |

Activate the pre-commit hook once per clone: `git config core.hooksPath .githooks` (runs `lint-dark-mode.php` over staged `*.blade.php`).

## Test environments

- **PHPUnit** (`phpunit.xml`): forces `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`, `BCRYPT_ROUNDS=4`, `QUEUE_CONNECTION=sync`, mail `array`. Tests boot via `tests/TestCase.php`, which calls `withoutVite()` and a `gc_collect_cycles()` in tearDown to keep memory bounded — keep that in mind if you add fixtures that retain references.
- **Playwright**: uses `APP_ENV=e2e` and `.env.e2e`, which points at `database/e2e.sqlite`. Tests run **serially with 1 worker** because the sqlite file and sessions are shared. `tests/e2e/global-setup.ts` calls `scripts/e2e-reset.sh` which does `migrate:fresh --seed`. The webServer is `php -S 127.0.0.1:8001 -t public` — not artisan serve. `E2E_MODE=true` is the flag tests use to skip mail/webhook dispatch.
- Playwright has three projects: `chromium-light` (excludes `dark-mode.spec.ts` and `mobile.spec.ts`), `chromium-dark` (only dark-mode spec), `mobile` (iPhone-13 viewport on chromium, only mobile spec).

## Architecture

**Routes map directly to Livewire components**, not controllers (`routes/web.php`). The 15 components in `app/Livewire/` *are* the page handlers — `Dashboard`, `ProjectIndex`, `ProjectKanban`, `ProjectTimeline`, `ProjectForm` (handles both create and edit via optional `{projectId}`), `ProjectHistory`, `ProjectTypeIndex`, `UserIndex`, `Documentation`, `NotificationCenter`, `Onboarding`, etc. The only traditional controllers are `ImpersonationController`, `ProjectPdfController`, `PortfolioPdfController`, plus the Auth/WebAuthn scaffolding. WebAuthn routes come from `WebAuthnRoutes::register()`.

**Authorization**: single `ProjectPolicy`. `before()` short-circuits all checks to true for admins. Managers can `view` any project but only `update`/`delete` ones they `created_by`. Users can only view/edit their own. The role enum lives on `User` (`ROLE_ADMIN`/`ROLE_MANAGER`/`ROLE_USER`) and there is a legacy `is_admin` boolean column — `User::isAdmin()` accepts either, so don't assume only one is set.

**Project domain model** (`app/Models/Project.php`):
- `phases`, `risks`, `nextSteps`, `comments`, `snapshots` (all hasMany, ordered by `sort_order` or `created_at`).
- `Project::$phaseKeys` is a fixed array of seven phase identifiers (`instalacija_analiza`, `funkcionalna_specifikacija`, `implementacija_testiranje`, `integracije`, `uat_edukacija`, `go_live`, `hypercare`). **These are identifier keys, not user-facing strings** — display labels come from translation files (`__('projects.phases.<key>')`). The legacy `$phaseLabels` static array exists for code paths that don't have access to the translator; don't introduce new code that depends on it.
- `createSnapshot($userId, $changeNote)` is the canonical way to record history — it materializes phases/risks/nextSteps into `snapshot_data`. `ProjectHistory` diffs these.

**i18n**: three locales (`hr`, `en`, `de`) in `lang/`. `SetLocale` middleware reads `session('locale')` first, then `user->locale`, only accepts the three known codes. Plus root-level JSON files for flat strings. Both must be updated when adding strings.

**Scheduled work** (`routes/console.php`): `projects:send-alerts` daily at 08:00, `projects:weekly-report` Mondays at 07:00. Requires a real scheduler / queue worker — `composer dev` runs `queue:listen`.

## Hard requirements when changing code

- **Dark mode is enforced**, not optional. Every Blade `class="…"` that uses `bg-white`, `bg-gray-{50,100,200}`, `text-gray-{700,800,900}`, `text-black`, or `border-gray-{100,200,300}` must have a paired `dark:` sibling in the same class string. The pre-commit hook and CI fail otherwise. Mail templates under `resources/views/vendor/mail/` and `resources/views/emails/` are exempt.
- **Source code language is English.** Comments, exception messages, log messages, variable names, translatable string *keys* — all English. Croatian only in `lang/hr/` locale files. (The `instalacija_analiza`-style phase keys predate this rule and are now part of the schema/translation keys; don't try to "translate" them in code.)
- New tests for new behavior; regression tests for bugfixes. Run `php artisan test` before pushing — CI is the same command.
- Do **not** add `Co-Authored-By` to commit messages. Keep subjects as one-liners.

## Notes on the working tree

- `data/` holds bind-mounted MySQL + storage for `docker compose` — never commit, never wipe without confirmation.
- `database/database.sqlite` and `database/e2e.sqlite` are local test DBs; safe to delete (re-created by migrations).
- `.phpunit.result.cache` is a phpunit artifact, ignore.
- `plans/` and `livewire4-migration-risk-report.txt` are user scratch notes — don't edit unless asked.
