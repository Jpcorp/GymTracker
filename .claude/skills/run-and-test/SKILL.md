---
name: run-and-test
description: Bring up GymTracker Pro (Laravel Sail), drive it in a real headless browser, and run its Pest suite. Use when asked to run, start, launch, or screenshot this app, or verify a change works end to end.
---

# Run and browser-test GymTracker Pro

Laravel 12 + Sail. Docker compose project name is **`repo`** (from the directory name). No PHP/Composer/Node needed on the host — everything runs through Docker, including the browser driver below.

Paths here are relative to `<repo-root>/` (i.e. `.claude/skills/run-and-test/driver.mjs`, not relative to this file).

## Prerequisites

Docker Desktop only. Verified in this session: `docker info` succeeds, `docker compose version` is v2.35+.

## Build / bring up

```bash
cd /mnt/c/develop/1.-Repositorios/GymTracker/repo
docker compose up -d          # reuses the cached sail-8.5/app image, no rebuild needed
until [ "$(docker inspect --format='{{.State.Health.Status}}' repo-mysql-1)" = healthy ]; do sleep 2; done
docker compose exec -T laravel.test php artisan migrate --force
```

App serves at **http://localhost** (`APP_PORT=80` in `.env`). Stop: `docker compose down` (add `-v` to also wipe the DB volume).

## Run (agent path) — the driver

The driver is `.claude/skills/run-and-test/driver.mjs`, a small chromium-cli-alike REPL (reads newline commands from stdin: `nav`, `wait-for`, `click`, `fill`, `select`, `press`, `screenshot`, `new-context`, `eval`, `console`, `quit`). No local browser is installed on this host, so it runs inside the official Playwright docker image via the wrapper script `.claude/skills/run-and-test/run-driver.sh`, which handles the `--add-host=host.docker.internal` networking and mounts.

**Verified this session (post dark-theme reskin)** — register a trainer, land on the dashboard, view the client list, create a client, and drive its tabbed show page (Métricas/Fotos/Evaluaciones/Gráfico/Asistencia — Alpine `x-show` tabs, content stays in the DOM). The UI is Spanish (`APP_LOCALE=es`), so assertions use the Spanish strings:

```bash
TS=$(date +%s)$RANDOM   # timestamp+random: plain $(date +%s) can collide across quick successive runs
.claude/skills/run-and-test/run-driver.sh /tmp/gt-screenshots <<EOF
nav /register
fill input[name="name"] Skill Verify Trainer
fill input[name="email"] skillverify.${TS}@example.com
fill input[name="password"] Password123!
fill input[name="password_confirmation"] Password123!
click button[type="submit"]
wait-for text=Panel
screenshot 01-dashboard
nav /clients
wait-for text=Clientes
screenshot 02-clients
click text=Nuevo Cliente
wait-for input[wire\:model="name"]
fill input[wire\:model="name"] Test Client
fill input[wire\:model="email"] testclient.${TS}@example.com
fill input[wire\:model="birth_date"] 1995-05-20
select select[wire\:model="gender"] female
fill input[wire\:model="start_date"] 2026-06-01
click button[type="submit"]
wait-for text=Test Client
screenshot 03-clients-list
click text="Ver"
wait-for text=Registrar Métrica Física
screenshot 04-client-show-metrics
click text=Fotos
screenshot 05-client-show-photos
click text=Asistencia
screenshot 06-client-show-attendance
console
quit
EOF
```

The card grid, badges, header, and tab bar are all part of the dark `bg-slate-950`/`bg-slate-900`/cyan-accent theme (see `resources/views/components/ui/*.blade.php`). A screenshot that still looks white/indigo means the Vite build wasn't refreshed after a Blade change — see "Rebuilding CSS after a Blade change" below.

Screenshots land in the directory passed as the first arg (`/tmp/gt-screenshots` above, defaults to `/tmp/gymtracker-screenshots` if omitted). Read them with the Read tool to actually look — a blank or error page is a failure, not a pass.

First invocation installs `playwright` into `.claude/skills/run-and-test/node_modules` (gitignored) — takes ~20s, one-time; subsequent runs skip straight to launching.

To test login with a fresh, unauthenticated session (e.g. after registering), send `new-context` before `nav /login` — don't try to click a logout button, Breeze's logout is a Livewire method call on a nav dropdown, not a plain form action, and is fragile to select.

## Rebuilding CSS after a Blade change

There's no local Node, and `/var/www/html` is bind-mounted from the Windows drive (`/mnt/c/...`) — running `npm install`/`npm run build` straight there is slow (thousands of small file writes over the 9p mount, per `CLAUDE.md`'s WSL2 note). Instead, build on the container's own native filesystem (fast) and copy just the output back over the bind mount:

```bash
docker compose exec -T laravel.test bash -lc "
  rm -rf /tmp/build && mkdir -p /tmp/build
  cp -r /var/www/html/resources /var/www/html/package.json /var/www/html/package-lock.json \
        /var/www/html/tailwind.config.js /var/www/html/postcss.config.js /var/www/html/vite.config.js /tmp/build/
  cd /tmp/build && npm ci --no-audit --no-fund && npx vite build
"
docker compose exec -T laravel.test bash -lc "rm -rf /var/www/html/public/build && cp -r /tmp/build/public/build /var/www/html/public/build"
```

Re-run just the `cp -r resources ... && npx vite build` half (skip `npm ci`) on subsequent Blade-only edits — `node_modules` in `/tmp/build` survives between `docker compose exec` calls as long as the container itself isn't restarted.

## Run (human path)

Open `http://localhost` in a real browser once the containers are up. Nothing else needed — no separate frontend dev server; assets are pre-built (`public/build/`). For live frontend editing: `npm install && npm run dev` (or `sail npm run dev`).

## Test suite

```bash
docker compose exec -T laravel.test php vendor/bin/pest
```

Project convention: every feature ships with its own Pest test (see `CLAUDE.md`). Run this after any change — it's the fast sanity check; the driver above is for confirming the actual rendered UI.

## Gotchas

- **Breeze requires lowercase email** (`'email' => ['lowercase', ...]` validation rule) — an uppercase segment in a test email (e.g. `foo.PLACEHOLDER@example.com`) fails registration with "El campo correo electrónico debe estar en minúscula." (Spanish validation message), not a form-not-found error. Use `$(date +%s)` or similar for uniqueness, never uppercase.
- **Mount `/out` separately from the work dir.** `page.screenshot({path: '/out/...'})` "succeeds" even if `/out` isn't bind-mounted to the host — it just writes inside the throwaway container and is lost on `--rm`. `run-driver.sh` mounts both `/work` (the driver + node_modules) and `/out` (screenshots) — don't collapse them into one mount.
- **Use `http://host.docker.internal`, not `http://localhost`,** as the driver's base URL — the browser runs in a *separate* container from the app, so it must reach the app via the host's published port, not its own loopback. `run-driver.sh` sets this by default; override with `BASE_URL` if needed.
- **Host Redis port 6379 was already taken** by an unrelated container on this dev machine, so `.env` has `FORWARD_REDIS_PORT=6380` (internal container-to-container `REDIS_PORT` is untouched, still 6379). If `mysql`/`redis` fail to start with a port-bind error, check for other docker projects holding 3306/6379/80.
- **`wire:navigate` (Livewire SPA-style redirects)** update the URL via pushState — `page.url()` / `wait-for` work fine against them, no special handling needed, but give them a beat: `waitForLoadState('networkidle')` (already built into the driver's `nav`) is enough, a raw `sleep` is not.
- **`click text=Foo` does unquoted substring matching** (the driver passes it straight to `page.locator()`, which treats bare `text=` as a case-insensitive substring engine) — `click text=Ver` on the client list card can match "Reskin **Ver**ify Two" in the trainer name (rendered above the actual "Ver" link) before it matches the link itself, and then hangs for the full 30s actionability timeout clicking something that isn't interactive. Quote it for an exact match instead: `click text="Ver"`.

## Troubleshooting

| Symptom | Fix |
|---|---|
| `Cannot find module 'playwright'` | `run-driver.sh` didn't finish its one-time `npm install` — check for a `node_modules/playwright` dir under this skill folder; delete a half-installed one and re-run. |
| `wait-for` times out right after a register/login click | Screenshot the page (the driver already does one on the next line in the example above) and read it — usually a validation error banner, not a broken app. |
| Driver hangs with no output | You're missing a trailing `quit`/`exit` command in the heredoc — the REPL waits for stdin to close otherwise. |
