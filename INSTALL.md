# Project Status - Offline Installation Guide

## Requirements

- **OS:** Linux (Debian/Ubuntu recommended), Windows with Docker Desktop, or macOS
- **Architecture:** x86_64 (amd64) **or** arm64 — the published image is multi-arch (amd64 + arm64). Mac M1/M2/M3, AWS Graviton, Raspberry Pi 4/5 are all supported.
- **Docker Engine:** 24.0+ with Docker Compose plugin (v2)
- **Disk:** ~500 MB free (images + database)
- **RAM:** 1 GB minimum, 2 GB recommended
- **Internet:** only needed at first pull (or for the optional Microsoft Entra SSO). Once images are local, the app runs entirely offline.

### Two distribution modes

This guide covers both ways to install:

1. **Direct pull from GHCR** (recommended when the server has internet at install time)
   ```bash
   docker compose pull
   docker compose up -d
   ```
   See [QUICKSTART.md](QUICKSTART.md) for the abbreviated version of this path.

2. **Offline tarball** (for air-gapped servers — bundle once on a machine with internet, install on the offline target)
   - Step-by-step bundle creation is in the **[Creating an offline bundle](#creating-an-offline-bundle)** section below.

### Install Docker (if not already installed)

**Linux (Debian/Ubuntu):**
```bash
# 1. Install Docker Engine
sudo apt update
sudo apt install -y ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/debian \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# 2. Allow your user to run Docker without sudo
sudo usermod -aG docker $USER
newgrp docker

# 3. Verify
docker --version
docker compose version
```

For Ubuntu, replace `debian` with `ubuntu` in the repository URL above.

**Windows:**
1. Download Docker Desktop from https://www.docker.com/products/docker-desktop/
2. Run the installer and restart when prompted
3. Open Docker Desktop and wait for it to start
4. Open PowerShell and verify: `docker --version`

**macOS:**
1. Download Docker Desktop from https://www.docker.com/products/docker-desktop/
2. Drag to Applications, open it, and allow the privileged helper
3. Verify in Terminal: `docker --version`

## Quick Start

### Path A — Direct pull from GHCR (server has internet)

```bash
# 1. Clone (or download just docker-compose.yml + .env.production.example — see QUICKSTART.md)
git clone https://github.com/fvisic/ProjectStatusApp.git
cd ProjectStatusApp

# 2. Configure
cp .env.production.example .env
$EDITOR .env                          # set APP_KEY, DB_PASSWORD, DB_ROOT_PASSWORD, APP_URL

# 3. Pull the latest stable image + start
docker compose pull
docker compose up -d

# 4. Open in browser
# http://localhost:54322 (or whatever APP_URL you set)
```

### Path B — Offline tarball (air-gapped target)

```bash
# 1. Extract the bundle prepared on a machine with internet
tar xzf projectstatus-offline.tar.gz
cd projectstatus-offline

# 2. Load the images into the local Docker daemon
docker load -i projectstatus-app.tar.gz
docker load -i mysql-8.4.tar.gz

# 3. Start
docker compose up -d

# 4. Open in browser
# http://localhost:54322
```

The bundle is created on a separate machine; see **[Creating an offline bundle](#creating-an-offline-bundle)** below.

---

The first boot will:
- Load the Docker images (~80 MB app + ~160 MB MySQL)
- Start MySQL and wait for it to be healthy
- Run database migrations automatically
- Seed demo data (20 projects, 4 users) if `RUN_SEED=1` is set

## Creating an offline bundle

Do this on a workstation with internet, then transfer the resulting `.tar.gz` to the offline server.

```bash
# 1. Pull the multi-arch image (or pin to a specific stable version)
docker pull ghcr.io/fvisic/projectstatusapp:latest
docker pull mysql:8.4

# Note: --platform may be specified if the target is a different arch:
#   docker pull --platform linux/amd64 ghcr.io/fvisic/projectstatusapp:latest

# 2. Save images to compressed tarballs
docker save ghcr.io/fvisic/projectstatusapp:latest | gzip > projectstatus-app.tar.gz
docker save mysql:8.4 | gzip > mysql-8.4.tar.gz

# 3. Grab the compose + env files
curl -O https://raw.githubusercontent.com/fvisic/ProjectStatusApp/main/docker-compose.yml
curl -O https://raw.githubusercontent.com/fvisic/ProjectStatusApp/main/.env.production.example
mv .env.production.example .env

# 4. Edit docker-compose.yml to point at the local image name (so Docker doesn't try to pull again)
sed -i 's|image: projectstatus:latest|image: ghcr.io/fvisic/projectstatusapp:latest|' docker-compose.yml

# 5. Bundle everything together
mkdir -p projectstatus-offline
mv projectstatus-app.tar.gz mysql-8.4.tar.gz docker-compose.yml .env projectstatus-offline/
tar czf projectstatus-offline.tar.gz projectstatus-offline/
```

Resulting `projectstatus-offline.tar.gz` is what gets shipped to the air-gapped target. On that target, follow **Path B** above.

## Default Users

| Email                | Password     | Role    |
|----------------------|--------------|---------|
| sarah@example.com    | `Demo1234!`  | Admin   |
| james@example.com    | `Demo1234!`  | Manager |
| priya@example.com    | `Demo1234!`  | User    |
| tom@example.com      | `Demo1234!`  | User    |

## Configuration

All settings are in the `.env` file. Open it with any text editor (`nano .env` on Linux, or Notepad on Windows).

### Change the port

Edit `.env`:
```
APP_PORT=3000
```
Then restart: `docker compose down && docker compose up -d`

### Change the URL (for LAN access)

If other machines on the network need to access the app:

1. Find your server's LAN IP:
```bash
# Linux
hostname -I | awk '{print $1}'

# macOS
ipconfig getifaddr en0

# Windows (PowerShell)
(Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.InterfaceAlias -notlike "*Loopback*" }).IPAddress
```

2. Edit `.env`:
```
APP_URL=http://192.168.1.100:8080
APP_PORT=8080
```
Replace `192.168.1.100` with your actual IP from step 1.

3. Open the firewall port (Linux only):
```bash
# UFW (Debian/Ubuntu default)
sudo ufw allow 8080/tcp

# Or firewalld (CentOS/RHEL)
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --reload
```

4. Restart: `docker compose down && docker compose up -d`

Other machines can now open `http://192.168.1.100:8080` in their browser.

### Disable demo data

To start with an empty database, edit `.env` before the first boot:
```
RUN_SEED=0
```

### Database credentials

The default credentials are in `.env`. Change them **before the first boot** (before MySQL initializes the data directory):
```
DB_PASSWORD=YourNewPassword
DB_ROOT_PASSWORD=YourNewRootPassword
```

### Microsoft Entra ID (Azure AD) SSO — optional

**Requires internet.** Entra OAuth runs against `login.microsoftonline.com`, so this is the only auth method that does **not** work in a fully offline deployment. Leave the four variables blank to disable — the "Sign in with Microsoft" button on `/login` only renders when all three of `MICROSOFT_ENTRA_SSO_TENANT_ID`, `_CLIENT_ID`, `_CLIENT_SECRET` are set. Existing email / passkey / 2FA flows continue to work unchanged.

**1. Register the app in Azure**
- Azure Portal → **App registrations** → **New registration**
- Authentication → Web platform → Redirect URI: **`https://your-domain/sso/microsoft/web/callback`** (the path is fixed — must include `/sso/microsoft/web/callback`)
- Certificates & secrets → **New client secret** → copy the *Value* immediately (only visible once; expires per your Azure tenant policy, typically 6–24 months)
- From Overview, copy **Application (client) ID** and **Directory (tenant) ID**

**2. Set in `.env` (must restart container to apply)**
```env
MICROSOFT_ENTRA_SSO_TENANT_ID=<directory-tenant-id-guid>
MICROSOFT_ENTRA_SSO_CLIENT_ID=<application-client-id-guid>
MICROSOFT_ENTRA_SSO_CLIENT_SECRET=<the-secret-value>
MICROSOFT_ENTRA_SSO_REDIRECT_URI=https://your-domain/sso/microsoft/web/callback
```

`MICROSOFT_ENTRA_SSO_REDIRECT_URI` must **exactly** match the Redirect URI registered in Azure (scheme, host, port, path). Mismatch = OAuth rejects the callback.

**3. Behavior on first SSO login**
- Looks up `users` by `email`
- If a user with that email exists → links Microsoft identity to the existing account
- If not → auto-creates a verified user (`auto_register: true` in `config/microsoft-entra-sso.php` — set `false` to require pre-existing accounts only)

**Notes:**
- Without credentials configured, `php artisan route:list` and direct hits to `/sso/microsoft/*` return errors because the upstream package validates config in its service constructor. This is harmless for normal traffic — the login UI hides the SSO routes from users until credentials are set.
- For LAN deployments with a fake hostname (e.g. `ps.your-lan.local`), Entra requires HTTPS on the redirect URI. Use the nginx self-signed cert path in this document, or Caddy + a real domain.

## Managing the Application

```bash
# Stop
docker compose down

# Start (after stop)
docker compose up -d

# View logs
docker compose logs -f app

# Restart
docker compose restart app

# Full reset (deletes all data!)
docker compose down -v
rm -rf data/
./start.sh
```

## Backup & Restore

### Backup
```bash
docker exec projectstatus-db mysqldump -u projectstatus -p"$DB_PASSWORD" projectstatus | gzip > backup_$(date +%Y%m%d).sql.gz
```

### Restore
```bash
gunzip -c backup_20260415.sql.gz | docker exec -i projectstatus-db mysql -u projectstatus -p"$DB_PASSWORD" projectstatus
```

**Note:** The example above reads the password from a shell variable. Set it first (`export DB_PASSWORD="..."`), or substitute your actual password inline. Never commit a real password into a script.

### Automatic daily backup (Linux)

To back up every night at 23:00:

```bash
# Open crontab editor
crontab -e

# Add this line (adjust the path to your installation directory):
0 23 * * * cd /path/to/projectstatus-offline && docker exec projectstatus-db mysqldump -u projectstatus -p"$DB_PASSWORD" projectstatus | gzip > backups/backup_$(date +\%Y\%m\%d).sql.gz && find backups/ -name "backup_*.sql.gz" -mtime +14 -delete
```

This keeps the last 14 days of backups and automatically deletes older ones.

## HTTPS Setup (optional)

HTTPS is required for WebAuthn (passkeys). For local/LAN use without passkeys, HTTP works fine for all other features.

**What is a reverse proxy?** Nginx sits in front of the Docker container and handles HTTPS encryption. Browsers connect to Nginx (port 443), and Nginx forwards the request to the app (port 8080). This way the app itself doesn't need to handle SSL certificates.

```
Browser --HTTPS (443)--> Nginx --HTTP (8080)--> Docker App
```

### Option A: Nginx + Let's Encrypt (recommended if server has internet + domain)

**Prerequisites:**
- The server must be reachable from the internet on port 80
- You must have a domain name (e.g., `status.yourcompany.com`) with DNS pointing to this server's public IP
- To check: `ping status.yourcompany.com` should show your server's IP

```bash
# 1. Install nginx and certbot
sudo apt update && sudo apt install -y nginx certbot python3-certbot-nginx

# 2. Open firewall ports for HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# 3. Create nginx config (replace YOUR_DOMAIN with your actual domain)
sudo tee /etc/nginx/sites-available/projectstatus.conf > /dev/null <<'NGINX'
server {
    listen 80;
    server_name YOUR_DOMAIN;

    client_max_body_size 20M;

    location /.well-known/acme-challenge/ {
        root /var/www/html;
    }

    # Gzip compression for responses not already compressed by the app container
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_min_length 256;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml
        application/xml+rss
        application/wasm
        application/manifest+json
        image/svg+xml
        font/ttf
        font/otf;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $host;
        proxy_read_timeout 120s;
        proxy_send_timeout 120s;
        proxy_buffering off;
    }
}
NGINX

# 4. Enable site and test config
sudo ln -sf /etc/nginx/sites-available/projectstatus.conf /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl restart nginx

# 5. Generate Let's Encrypt certificate (must have port 80 open + DNS pointing to server)
sudo certbot --nginx -d YOUR_DOMAIN

# Certbot will automatically:
#   - obtain the certificate
#   - modify nginx config to add SSL
#   - set up HTTP -> HTTPS redirect
#   - install a cron for auto-renewal (runs twice daily)

# 6. Verify auto-renewal works
sudo certbot renew --dry-run
```

Then update `.env`:
```
APP_URL=https://YOUR_DOMAIN
WEBAUTHN_ID=YOUR_DOMAIN
WEBAUTHN_ORIGINS=https://YOUR_DOMAIN
TRUSTED_PROXIES=127.0.0.1
```

Restart: `docker compose down && docker compose up -d`

### Option B: Nginx with self-signed certificate (for LAN / no internet)

Use this when the server has no internet access or no domain name. Works well for internal/LAN deployments.

First, find your server's LAN IP and replace `192.168.1.100` everywhere below:
```bash
hostname -I | awk '{print $1}'
```

```bash
# 1. Install nginx and openssl
sudo apt update && sudo apt install -y nginx openssl

# 2. Open firewall ports
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# 3. Generate self-signed certificate (valid 10 years)
sudo mkdir -p /etc/nginx/ssl
sudo openssl req -x509 -nodes -days 3650 \
  -newkey rsa:2048 \
  -keyout /etc/nginx/ssl/projectstatus.key \
  -out /etc/nginx/ssl/projectstatus.crt \
  -subj "/CN=projectstatus" \
  -addext "subjectAltName=IP:192.168.1.100"

# 4. Create nginx config
sudo tee /etc/nginx/sites-available/projectstatus.conf > /dev/null <<'NGINX'
server {
    listen 443 ssl;
    server_name 192.168.1.100;

    ssl_certificate     /etc/nginx/ssl/projectstatus.crt;
    ssl_certificate_key /etc/nginx/ssl/projectstatus.key;

    client_max_body_size 20M;

    # Gzip compression for responses not already compressed by the app container
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_min_length 256;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml
        application/xml+rss
        application/wasm
        application/manifest+json
        image/svg+xml
        font/ttf
        font/otf;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $host;
        proxy_read_timeout 120s;
        proxy_send_timeout 120s;
        proxy_buffering off;
    }
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name 192.168.1.100;
    return 301 https://$host$request_uri;
}
NGINX

# 5. Enable and start
sudo ln -sf /etc/nginx/sites-available/projectstatus.conf /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl restart nginx
```

Replace `192.168.1.100` with your server's LAN IP everywhere (steps 3, 4, and `.env` below).

**Note:** Self-signed certificates will show a browser warning on first visit. Click "Advanced" and "Accept the risk" to continue. This is normal for LAN deployments.

### Option C: Caddy (simpler alternative, auto-generates certificate)

Caddy is easier to set up but less common in production. It automatically generates a self-signed certificate.

```bash
sudo apt install caddy

# Open firewall ports
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

echo 'https://192.168.1.100 { reverse_proxy localhost:8080; tls internal }' | sudo tee /etc/caddy/Caddyfile
sudo systemctl restart caddy
```

Replace `192.168.1.100` with your server's LAN IP.

### After HTTPS is configured

Update `.env` and restart:
```bash
# Edit .env
APP_URL=https://192.168.1.100
WEBAUTHN_ID=192.168.1.100
WEBAUTHN_ORIGINS=https://192.168.1.100
TRUSTED_PROXIES=127.0.0.1

# Restart app to pick up changes
docker compose down && docker compose up -d
```

Replace `192.168.1.100` with your actual server IP or domain name.

## Troubleshooting

**"Port 8080 already in use"**
Change `APP_PORT` in `.env` to another port (e.g., 3000). Then restart.

**"Permission denied" on Docker commands**
```bash
# Add your user to the docker group
sudo usermod -aG docker $USER
# Log out and back in, or run:
newgrp docker
```

**"Permission denied" on data directory**
```bash
sudo chown -R 82:82 data/storage
```

**App shows 500 error**
Check logs: `docker compose logs app --tail=50`

**Database not ready**
Wait 30 seconds and refresh. First boot takes longer while MySQL initializes.

**Cannot access from other machines on the network**
1. Check firewall: `sudo ufw status` - port 8080 must be allowed
2. Check `.env` has `APP_URL` set to your LAN IP, not `localhost`
3. Verify the server IP: `hostname -I`
4. Try from the server itself first: `curl http://localhost:8080`

**Images fail to load with "exec format error"**
The image is multi-arch (amd64 + arm64), so this should be rare. If you built an offline tarball on a different architecture than your target, re-create it with `docker pull --platform=linux/<target-arch>` first (see the "Creating an offline bundle" section).

## System Requirements by User Count

| Users    | RAM   | CPU   |
|----------|-------|-------|
| 1-5      | 1 GB  | 1 vCPU |
| 5-20     | 2 GB  | 2 vCPU |
| 20-50    | 4 GB  | 2 vCPU |
