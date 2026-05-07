# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

---

## [1.0.1] — 2026-05-07

### Changed
- Updated dependencies: axios 1.16, postcss 8.5.14, vite 8.0.11, laravel/framework 13.8, maatwebsite/excel 3.1.69

### Fixed
- CI workflow now runs on PHP 8.4 to match Symfony 8.x requirements
- Dashboard tests use locale-independent translation keys
- GitHub Actions CI workflow has explicit `permissions: contents: read`

---

## [1.0.0] — 2026-05-07

### Added
- Project CRUD with health tracking (on track / at risk / off track)
- Dynamic project types — admin-managed with color picker and soft delete
- Dashboard with KPI cards, health trend chart, and burndown chart
- Kanban board with drag-and-drop health updates
- Gantt timeline view with zoom controls and phase visualization
- Role-based access control: admin / manager / user
- Admin impersonation of any user account
- Login with email or username
- TOTP 2FA (Google Authenticator compatible)
- WebAuthn passkey authentication (Touch ID, Windows Hello, YubiKey)
- Excel and PDF export for individual projects
- Portfolio PDF report
- Email notifications and scheduled weekly digest
- Project snapshots and history diffing
- Dark mode
- Trilingual UI: English, German, Croatian
- Docker deployment (nginx + PHP-FPM, multi-platform amd64/arm64)
- Auto-seed demo data on first boot via `RUN_SEED=1`
- `app:create-admin` Artisan command for headless provisioning
- Stale status warning and overdue row highlights in project list
- Manager read-only view for projects they did not create
- Username field on registration form

[Unreleased]: https://github.com/fvisic/ProjectStatusApp/compare/v1.0.1...HEAD
[1.0.1]: https://github.com/fvisic/ProjectStatusApp/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/fvisic/ProjectStatusApp/releases/tag/v1.0.0
