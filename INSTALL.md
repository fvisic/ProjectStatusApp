# Project Status - Offline Installation Guide

## Requirements

- **OS:** Linux (Debian/Ubuntu recommended), Windows with Docker Desktop, or macOS
- **Architecture:** x86_64 (amd64) - the bundled images are built for this architecture
- **Docker Engine:** 24.0+ with Docker Compose plugin (v2)
- **Disk:** ~500 MB free (images + database)
- **RAM:** 1 GB minimum, 2 GB recommended
- **No internet required** - all images are included in this package

### Check your architecture

The bundled Docker images only work on x86_64 (amd64) machines. Before starting, verify your architecture:

```bash
# Linux / macOS
uname -m
# Expected output: x86_64

# Windows (PowerShell)
echo $env:PROCESSOR_ARCHITECTURE
# Expected output: AMD64
```

If you see `aarch64`, `arm64`, or `ARM` - these images will not work. Contact the vendor for an ARM-compatible build.

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

### Linux / macOS

```bash
# 1. Extract the package
tar xzf projectstatus-offline.tar.gz
cd projectstatus-offline

# 2. Run the installer
chmod +x start.sh
./start.sh

# 3. Open in browser
# http://localhost:8080
```

### Windows (PowerShell)

```powershell
# 1. Extract the package (use 7-Zip or WSL for .tar.gz)
# With 7-Zip: right-click projectstatus-offline.tar.gz -> Extract Here (twice)
# Or in WSL: tar xzf projectstatus-offline.tar.gz

# 2. Open PowerShell in the extracted folder
cd projectstatus-offline

# 3. Start the services
docker compose up -d

# 4. Open in browser
# http://localhost:8080
```

**Note:** On Windows, `start.sh` will not run natively. The script only loads Docker images and runs `docker compose up -d`. You can load images manually:

```powershell
docker load -i projectstatus-app.tar.gz
docker load -i mysql-8.4.tar.gz
docker compose up -d
```

---

The first boot will:
- Load the Docker images (~80 MB app + ~160 MB MySQL)
- Start MySQL and wait for it to be healthy
- Run database migrations automatically
- Seed demo data (20 projects, 4 users)

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
docker exec projectstatus-db mysqldump -u projectstatus -p'Pr0jectStatus!2026' projectstatus | gzip > backup_$(date +%Y%m%d).sql.gz
```

### Restore
```bash
gunzip -c backup_20260415.sql.gz | docker exec -i projectstatus-db mysql -u projectstatus -p'Pr0jectStatus!2026' projectstatus
```

**Note:** If you changed `DB_PASSWORD` in `.env`, use your password instead of `Pr0jectStatus!2026`.

### Automatic daily backup (Linux)

To back up every night at 23:00:

```bash
# Open crontab editor
crontab -e

# Add this line (adjust the path to your installation directory):
0 23 * * * cd /path/to/projectstatus-offline && docker exec projectstatus-db mysqldump -u projectstatus -p'Pr0jectStatus!2026' projectstatus | gzip > backups/backup_$(date +\%Y\%m\%d).sql.gz && find backups/ -name "backup_*.sql.gz" -mtime +14 -delete
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
Your machine is not x86_64. Run `uname -m` to check. These images only work on x86_64 (amd64) machines.

## System Requirements by User Count

| Users    | RAM   | CPU   |
|----------|-------|-------|
| 1-5      | 1 GB  | 1 vCPU |
| 5-20     | 2 GB  | 2 vCPU |
| 20-50    | 4 GB  | 2 vCPU |
