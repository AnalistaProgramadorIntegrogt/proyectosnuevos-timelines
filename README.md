# Proyectos Nuevos Hub

Aplicación Laravel 13 + Jetstream/Livewire, con base de datos PostgreSQL, servida a través de PHP-FPM + Nginx. Se utiliza Docker Compose tanto para el desarrollo local como para producción.

Existen dos stacks de Compose separados:

- `docker-compose.local.yml` — desarrollo local (este repositorio, en tu máquina)
- `docker-compose.yml` — producción (se ejecuta en el servidor detrás de Caddy)

They do not share containers, networks, or volumes, so both can exist without
conflicting.

---

## Local development setup

### Prerequisites

- Docker + Docker Compose v2
- Nothing else — PHP, Composer and Node all run inside the containers.

### Files involved

| File | Purpose |
|---|---|
| `src/.env` | Local Laravel env (`APP_ENV=local`, debug on, generated `APP_KEY`, DB pointed at the `db` service). Not committed (see `.gitignore`). |
| `docker-compose.local.yml` | Self-contained local stack: internal-only bridge network (no dependency on the external `caddy` network used in prod), ports exposed to the host, dependencies auto-installed on first boot. |
| `entrypoint.dev.sh` | Dev container entrypoint: installs Composer deps if `vendor/` is missing, generates `APP_KEY` if needed, installs npm deps and runs `vite build` if `public/build` is missing, waits for Postgres, runs migrations, seeds on first run only (guarded by `storage/.seeded`), clears caches, starts PHP-FPM. |
| `nginx.local.conf` | Local nginx vhost — `server_name localhost`, plain HTTP (the production vhost forces HTTPS fastcgi params, which breaks local http). |

### First run

```bash
docker compose -f docker-compose.local.yml up -d --build
```

First boot takes a few minutes: it runs `composer install`, `npm install`,
and `vite build` inside the `app` container, then migrates and seeds the
database. Watch progress with:

```bash
docker compose -f docker-compose.local.yml logs -f app
```

Once you see `Starting PHP-FPM...` / `ready to handle connections`, the app
is available at **http://localhost:8080**.

### Daily commands

```bash
# start / stop
docker compose -f docker-compose.local.yml up -d
docker compose -f docker-compose.local.yml down        # keeps DB volume
docker compose -f docker-compose.local.yml down -v     # also wipes DB

# logs
docker compose -f docker-compose.local.yml logs -f app

# artisan / composer / npm inside the app container
docker compose -f docker-compose.local.yml exec app php artisan <command>
docker compose -f docker-compose.local.yml exec app composer <command>
docker compose -f docker-compose.local.yml exec app npm run dev   # Vite dev server (hot reload)
```

`./src` is bind-mounted into the container, so edits on the host are picked
up immediately; `vendor/` and `node_modules/` are installed into that same
mounted directory the first time the container boots.

### Seeded accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@integro.net.gt` | `Admin2026!` |
| Usuario | `usuario@integro.net.gt` | `Usuario2026!` |
| Invitado | `invitado@integro.net.gt` | `Invitado2026!` |

### Ports

| Service | Host port | Notes |
|---|---|---|
| `web` (nginx) | `8080` → `80` | app entrypoint, http://localhost:8080 |
| `db` (postgres) | `5432` → `5432` | connect with any Postgres client using `proyectos` / `proyectos_secret_2026` |
| `app` (php-fpm) | none exposed | only reachable from `web` over the internal network |

---

## Production overview

Production runs on the server via the root `docker-compose.yml`, sitting
behind a shared **Caddy** reverse proxy (external Docker network named
`caddy`, which must already exist on the host — it is not created by this
repo).

### Services

- **`app`** — `php:8.3-fpm-alpine` built from `Dockerfile`. Installs PHP
  extensions (`pdo_pgsql`, `zip`, `mbstring`, `gd`, `xml`, `bcmath`),
  Composer, and Node 20. Entry point is `entrypoint.sh`, which:
  - fixes `storage`/`bootstrap/cache` ownership for `www-data`
  - copies the Vite manifest into the location Laravel expects
  - waits for the database, runs `php artisan migrate --force`
  - seeds only once (guarded by `storage/.seeded`)
  - starts `php-fpm`
- **`web`** — `nginx:alpine`, serving `public/` and proxying `*.php` to
  `app:9000`. Configured via `nginx.conf` — forces HTTPS fastcgi params,
  since Caddy terminates TLS in front of it. Labeled for Caddy's
  reverse-proxy auto-discovery (`caddy: projectscentral.integro.net.gt`).
- **`db`** — `postgres:16-alpine`, data persisted in the `pgdata` named
  volume, internal network only (not exposed to the host or the internet).

### Configuration

Environment variables are set directly in `docker-compose.yml` under the
`app` and `db` services (`APP_ENV=production`, `APP_DEBUG=false`,
`APP_URL=https://projectscentral.integro.net.gt`, DB credentials, etc.). A
root `.env.example` documents the same variables for reference, but the
compose file is the source of truth on the server.

`php.ini` raises upload limits (`50M`) and execution time (`300s`) for both
stacks, mounted read-only into the `app` container.

### Networks & volumes

- `caddy` — external network shared with the Caddy reverse proxy, connects
  `app` and `web` to it so Caddy can route traffic in.
- `internal` — private bridge network between `app`, `web`, and `db`.
- `pgdata` — named volume persisting Postgres data across deploys.

### Deploying

```bash
docker compose up -d --build
```

Note that, as committed, neither `vendor/` nor `node_modules/` are installed
by the production `Dockerfile`/`entrypoint.sh` — on the live server these are
expected to already be present in `./src` (installed manually or by a
separate deploy step). A from-scratch deploy of this repo would need that
install step added before `entrypoint.sh` can run migrations successfully.
