#!/usr/bin/env bash
# Atualiza o portal (git pull + migrações + catálogo + sitemap). Execute como claudeops.
set -euo pipefail
APP_DIR="${APP_DIR:-/var/www/portal-novo}"
BRANCH="${BRANCH:-main}"
cd "$APP_DIR"
git fetch --depth=1 origin "$BRANCH"
git reset --hard "origin/$BRANCH"
php bin/console migrate
php bin/console seed
php bin/console sitemap
echo "Atualizado: $(git log -1 --format='%h %s')"
