#!/usr/bin/env bash
# Reset the E2E database to a known seeded state.
# Used by Playwright between runs and by the global-setup script.

set -euo pipefail
cd "$(dirname "$0")/.."

export APP_ENV=e2e

# Make sure the sqlite file exists (artisan migrate:fresh will recreate schema).
mkdir -p database
[ -f database/e2e.sqlite ] || touch database/e2e.sqlite

php artisan migrate:fresh --seed --force --no-interaction >/tmp/e2e-reset.log 2>&1 \
    || { tail -40 /tmp/e2e-reset.log; echo "e2e reset failed"; exit 1; }

echo "e2e database reset OK"
