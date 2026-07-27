#!/bin/bash
# Pipes stdin (driver commands, one per line) into driver.mjs running inside
# the official Playwright docker image. See SKILL.md for usage/examples.
set -e
SKILL_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OUT_DIR="${1:-/tmp/gymtracker-screenshots}"
mkdir -p "$OUT_DIR"

if [ ! -d "$SKILL_DIR/node_modules/playwright" ]; then
  echo "First run: installing playwright into $SKILL_DIR/node_modules (cached for next time)..." >&2
  docker run --rm -v "$SKILL_DIR":/work -w /work \
    -e PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1 \
    mcr.microsoft.com/playwright:v1.55.0-jammy \
    npm install >&2
fi

docker run --rm -i \
  --add-host=host.docker.internal:host-gateway \
  -v "$SKILL_DIR":/work \
  -v "$OUT_DIR":/out \
  -w /work \
  -e OUT_DIR=/out \
  -e BASE_URL="${BASE_URL:-http://host.docker.internal}" \
  mcr.microsoft.com/playwright:v1.55.0-jammy \
  node driver.mjs
