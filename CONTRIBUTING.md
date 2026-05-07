# Contributing

Thank you for considering a contribution to Project Status. This document explains how to get involved effectively.

## Before You Start

- Check [open issues](https://github.com/fvisic/ProjectStatusApp/issues) to avoid duplicating work.
- For significant changes, open an issue first to discuss the approach before writing code.
- By contributing, you agree that your code will be licensed under [AGPL-3.0](LICENSE).

## Development Setup

```bash
git clone https://github.com/fvisic/ProjectStatusApp.git
cd ProjectStatusApp
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run dev
```

PHP 8.2+, Node 20+, and a MySQL/MariaDB database are required. See [INSTALL.md](INSTALL.md) for full prerequisites.

## How to Contribute

### Reporting Bugs

Open a GitHub issue with:
- Steps to reproduce
- Expected vs. actual behavior
- PHP version, browser, and deployment method (Docker / bare metal)
- Relevant logs or screenshots

### Suggesting Features

Open a GitHub issue labeled `enhancement`. Describe the use case, not just the feature — what problem does it solve and for whom?

### Submitting a Pull Request

1. Fork the repository and create a branch from `main`:
   ```bash
   git checkout -b fix/describe-the-fix
   ```

2. Make your changes. Keep commits focused — one logical change per commit.

3. Run the test suite before pushing:
   ```bash
   php artisan test
   ```

4. Push your branch and open a pull request against `main`. Fill in the PR description — what changed and why.

## Code Standards

- **Language:** all source code, comments, variable names, log messages, and exception messages must be in English. Translations belong in `lang/` files only.
- **Style:** follow existing PSR-12 conventions. Run `./vendor/bin/pint` before committing.
- **Tests:** new behavior should come with a test. Bug fixes should include a regression test where practical.
- **Blade/Livewire:** dark mode support is required for any UI change — use `dark:` Tailwind variants consistently.
- **No dead code:** don't leave commented-out code, unused imports, or `TODO` comments in submitted PRs.

## Translations

The app ships with English, German, and Croatian. To add or improve a translation:

1. Copy the relevant file from `lang/en/` into `lang/{locale}/`.
2. Translate all string values (keys stay in English).
3. Do the same for the flat JSON file (`lang/{locale}.json`) if it exists for that locale.

## Commit Messages

One-line summary in the imperative mood:

```
Fix PDF export failing when project name contains special characters
Add Spanish translation for dashboard strings
```

No `Co-Authored-By`, no issue numbers in the subject line (link them in the PR description instead).

## Questions

Open a [GitHub Discussion](https://github.com/fvisic/ProjectStatusApp/discussions) or an issue labeled `question`.
