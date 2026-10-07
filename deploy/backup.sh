#!/usr/bin/env bash
# Backup do banco do portal. Execute como claudeops (ex.: cron diário). Mantém 14 dias.
set -euo pipefail
APP_DIR="${APP_DIR:-/var/www/portal-novo}"
OUT="${OUT:-$HOME/backups-portal}"
mkdir -p "$OUT"; chmod 700 "$OUT"
set -a; . "$APP_DIR/.env"; set +a
mysqldump --single-transaction -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$OUT/portal-$(date +%F-%H%M).sql.gz"
find "$OUT" -name 'portal-*.sql.gz' -mtime +14 -delete
echo "Backup salvo em $OUT"
