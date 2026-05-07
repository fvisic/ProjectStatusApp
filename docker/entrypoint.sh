#!/bin/sh
set -e

cd /app

# Wait for MySQL to be ready (DB_HOST defaults to 'db' from compose)
if [ -n "${DB_HOST}" ] && [ "${DB_CONNECTION:-mysql}" = "mysql" ]; then
    echo "[entrypoint] waiting for MySQL at ${DB_HOST}:${DB_PORT:-3306}..."
    i=0
    until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT')?:3306).';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); } catch (Exception \$e) { exit(1); }" 2>/dev/null; do
        i=$((i+1))
        if [ "$i" -ge 60 ]; then
            echo "[entrypoint] MySQL did not become ready in time"
            exit 1
        fi
        sleep 2
    done
    echo "[entrypoint] MySQL is ready"
fi

# APP_KEY must come from the environment (docker-compose env_file).
# If missing, fail early — we do not want a fresh key on every restart.
if [ -z "${APP_KEY}" ] || [ "${APP_KEY}" = "base64:" ]; then
    echo "[entrypoint] ERROR: APP_KEY is not set. Generate one with:"
    echo "    docker run --rm projectstatus:latest php -r \"echo 'APP_KEY=base64:' . base64_encode(random_bytes(32)) . PHP_EOL;\""
    echo "    then add to .env and restart."
    exit 1
fi

# Laravel's config:cache writes to bootstrap/cache. Ensure there is no stale .env
# in the image but write runtime env vars so artisan picks them up.
# Quote values so spaces (e.g. APP_NAME="Project Status") parse correctly.
: > /app/.env
env | grep -E '^(APP_|DB_|LOG_|SESSION_|CACHE_|QUEUE_|BROADCAST_|FILESYSTEM_|MAIL_|TRUSTED_|RUN_SEED|WEBAUTHN_)' | while IFS='=' read -r key value; do
    # Quote value if it contains whitespace or special chars, and isn't already quoted
    case "$value" in
        \"*\") echo "${key}=${value}" ;;
        *[[:space:]]*|*\#*|*\$*) echo "${key}=\"${value}\"" ;;
        *) echo "${key}=${value}" ;;
    esac
done >> /app/.env

# Ensure storage tree exists on fresh volume mounts, then fix permissions
mkdir -p /app/storage/framework/cache/data \
         /app/storage/framework/sessions \
         /app/storage/framework/views \
         /app/storage/logs \
         /app/storage/app/public \
         /app/bootstrap/cache
chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# Laravel storage symlink
if [ ! -L /app/public/storage ]; then
    php artisan storage:link || true
fi

# Caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache || true

# Migrations (idempotent)
php artisan migrate --force --no-interaction

# Optional: seed on first boot if RUN_SEED=1 and marker file missing
if [ "${RUN_SEED:-0}" = "1" ] && [ ! -f /app/storage/.seeded ]; then
    echo "[entrypoint] running database seeder..."
    php artisan db:seed --force --no-interaction || echo "[entrypoint] seeder failed (continuing)"
    touch /app/storage/.seeded
fi

echo "[entrypoint] boot complete — handing off to supervisord"
exec "$@"
