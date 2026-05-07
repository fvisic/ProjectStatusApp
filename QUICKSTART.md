# Quickstart

Get Project Status running locally or on a server. The only prerequisite is Docker.

---

## Local (Mac / Windows / Linux)

### 1. Get the files

```bash
git clone git@github.com:fvisic/ProjectStatusApp.git
cd ProjectStatusApp
```

Or download just `docker-compose.yml` and `.env.production.example` without cloning the full repo:

```bash
mkdir project-status && cd project-status
curl -O https://raw.githubusercontent.com/fvisic/ProjectStatusApp/main/docker-compose.yml
curl -O https://raw.githubusercontent.com/fvisic/ProjectStatusApp/main/.env.production.example
```

### 2. Configure `.env`

```bash
cp .env.production.example .env
```

Open `.env` and set:

```env
APP_URL=http://localhost:54322

DB_PASSWORD=choose_a_strong_password
DB_ROOT_PASSWORD=choose_a_strong_root_password

SESSION_SECURE_COOKIE=false   # required for HTTP — set to true only when using HTTPS
```

### 3. Generate APP_KEY

```bash
docker run --rm --entrypoint php ghcr.io/fvisic/projectstatusapp:latest -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

Copy the output (starts with `base64:`) and paste it into `.env`:

```env
APP_KEY=base64:...
```

### 4. Start

```bash
docker compose up -d
```

> **Note:** `docker-compose.yml` uses `image: projectstatus:latest` by default. To run from the GHCR image without building locally, change it to:
> ```yaml
> image: ghcr.io/fvisic/projectstatusapp:latest
> ```

The app is available at **http://localhost:54322**

### 5. Create an admin user

```bash
docker compose exec app php artisan app:create-admin
```

Follow the prompts in the terminal (name, email, username, password).

---

## On a server (Linux)

### Prerequisites

```bash
# Ubuntu / Debian
apt install -y docker.io docker-compose-plugin
```

### 1. Prepare the directory

```bash
mkdir -p /var/www/project-status
cd /var/www/project-status
```

### 2. Download the files

```bash
curl -O https://raw.githubusercontent.com/fvisic/ProjectStatusApp/main/docker-compose.yml
curl -O https://raw.githubusercontent.com/fvisic/ProjectStatusApp/main/.env.production.example
cp .env.production.example .env
```

### 3. Configure `.env`

```env
APP_URL=https://your-domain.com
APP_KEY=base64:...          # generate as in step 3 above
SESSION_SECURE_COOKIE=true  # keep true for HTTPS

DB_PASSWORD=strong_password
DB_ROOT_PASSWORD=strong_root_password
```

### 4. Switch to the GHCR image in `docker-compose.yml`

```bash
sed -i 's|image: projectstatus:latest|image: ghcr.io/fvisic/projectstatusapp:latest|' docker-compose.yml
```

### 5. Start

```bash
docker compose up -d
```

The app listens on `127.0.0.1:54322`. Set up an nginx reverse proxy:

### 6. Nginx reverse proxy (HTTPS)

```nginx
server {
    listen 443 ssl;
    server_name your-domain.com;

    ssl_certificate     /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;

    location / {
        proxy_pass         http://127.0.0.1:54322;
        proxy_set_header   Host              $host;
        proxy_set_header   X-Real-IP         $remote_addr;
        proxy_set_header   X-Forwarded-For   $proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto $scheme;
    }
}

server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$host$request_uri;
}
```

SSL certificate with Let's Encrypt:

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx -d your-domain.com
```

### 7. Create an admin user

```bash
docker compose exec app php artisan app:create-admin
```

---

## Demo data (optional)

To start with demo projects and users, set the following in `.env` **before the first boot**:

```env
RUN_SEED=1
```

Demo users after seeding:

| Role    | Email               | Username      | Password     |
|---------|---------------------|---------------|--------------|
| Admin   | sarah@example.com   | sarah.chen    | `Demo1234!`  |
| Manager | james@example.com   | james.miller  | `Demo1234!`  |
| User    | priya@example.com   | priya.sharma  | `Demo1234!`  |
| User    | tom@example.com     | tom.weber     | `Demo1234!`  |

> Seeding runs **only once** — a marker file prevents it from running again on subsequent boots.

---

## Upgrading to a new version

```bash
docker compose pull   # pull the new image
docker compose up -d  # restart (migrations run automatically)
```

---

## Docker image

Multi-platform (amd64 + arm64) — works on Mac M1/M2/M3, Intel, and Linux servers.

```
ghcr.io/fvisic/projectstatusapp:latest
ghcr.io/fvisic/projectstatusapp:v1.2.0
```
