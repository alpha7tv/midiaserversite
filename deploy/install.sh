#!/usr/bin/env bash
# Instalação isolada da Cine Pulse no VPS. Uso:
#   bash install.sh check     -> só inspeciona (não altera nada)
#   bash install.sh install   -> instala/atualiza
set -euo pipefail
trap 'echo "ERRO na linha $LINENO: $BASH_COMMAND"' ERR
APP_DIR=/opt/cinepulse
DOMAIN=cinepulse.com.br
MODE=${1:-check}
SRC_DIR=$(cd "$(dirname "$0")/.." && pwd)

echo "== Portas em uso (TCP LISTEN) =="; ss -tlnp | awk 'NR>1{print $4, $6}' | sort -u
echo "== Containers =="; (docker ps --format '{{.Names}}\t{{.Image}}\t{{.Ports}}' 2>/dev/null || echo "docker não encontrado")
echo "== Quem ouve 80/443 =="; ss -tlnp '( sport = :80 or sport = :443 )' | tail -n +2 || true
echo "== nginx =="; (nginx -v 2>&1 && nginx -T 2>/dev/null | grep -E '^\s*server_name' | sort -u) || echo "nginx não encontrado no host"
echo "== certbot =="; (certbot --version 2>&1 || echo "certbot não instalado")
echo "== vhost cinepulse existente? =="; (grep -rl "$DOMAIN" /etc/nginx 2>/dev/null || echo "nenhum")
if [ "$MODE" = check ]; then exit 0; fi
echo "== Instalando =="

command -v docker >/dev/null || { echo "ERRO: docker não instalado"; exit 1; }
docker compose version >/dev/null 2>&1 || { echo "ERRO: plugin docker compose ausente"; exit 1; }
command -v nginx >/dev/null || { echo "ERRO: nginx do host não encontrado; instalação parada para não conflitar"; exit 1; }
if ss -tlnp '( sport = :80 )' | grep -q docker-proxy; then echo "ERRO: porta 80 pertence a um container (proxy em Docker). Parei para não conflitar."; exit 1; fi

mkdir -p "$APP_DIR"
tar -C "$SRC_DIR" --exclude node_modules --exclude data --exclude .env -cf - . | tar -C "$APP_DIR" -xf -
cd "$APP_DIR"

if [ ! -f .env ]; then
  PORT=3080; while ss -tln | awk '{print $4}' | grep -qE "[:.]$PORT$"; do PORT=$((PORT+1)); done
  PASS=$(od -An -N12 -tx1 /dev/urandom | tr -d ' \n')
  printf 'HOST_PORT=%s\nSITE_URL=https://%s\nADMIN_PASSWORD=%s\nSIGMA_URL=%s\n' "$PORT" "$DOMAIN" "$PASS" "${SIGMA_URL:-}" > .env
  chmod 600 .env
  echo ">> Senha inicial do admin: $PASS  (guardada em $APP_DIR/.env)"
fi
PORT=$(grep ^HOST_PORT .env | cut -d= -f2)

docker compose up -d --build
for i in $(seq 1 30); do curl -fsS "http://127.0.0.1:$PORT/healthz" >/dev/null 2>&1 && { echo ">> app ok na porta $PORT"; break; }; sleep 2; done

VHOST=/etc/nginx/sites-available/$DOMAIN
if [ -d /etc/nginx/sites-available ]; then
  [ -f "$VHOST" ] || sed "s/__PORT__/$PORT/" deploy/nginx-cinepulse.conf > "$VHOST"
  ln -sf "$VHOST" /etc/nginx/sites-enabled/$DOMAIN
else
  VHOST=/etc/nginx/conf.d/$DOMAIN.conf
  [ -f "$VHOST" ] || sed "s/__PORT__/$PORT/" deploy/nginx-cinepulse.conf > "$VHOST"
fi
nginx -t && systemctl reload nginx

if ! command -v certbot >/dev/null; then
  (apt-get update -qq && apt-get install -y -qq certbot python3-certbot-nginx) || echo "AVISO: não consegui instalar o certbot"
fi
if command -v certbot >/dev/null && ! grep -q "ssl_certificate" "$VHOST"; then
  certbot --nginx -d "$DOMAIN" -d "www.$DOMAIN" --non-interactive --agree-tos --register-unsafely-without-email --redirect || echo "AVISO: certbot falhou; o site segue em HTTP"
fi
nginx -t && systemctl reload nginx
echo "== Pronto: https://$DOMAIN  | admin: https://$DOMAIN/admin =="
