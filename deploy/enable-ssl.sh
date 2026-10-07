#!/usr/bin/env bash
# Emite o certificado SSL (Let's Encrypt, modo webroot) e ativa HTTPS no vhost do portal.
# Execute como ROOT. Não precisa do plugin nginx do certbot. Só mexe no vhost deste domínio.
set -euo pipefail

DOMAIN="${DOMAIN:-novo.midiaserver.com.br}"
APP_DIR="${APP_DIR:-/var/www/portal-novo}"
EMAIL="${EMAIL:-contato@midiaserver.com.br}"
VHOST="/etc/nginx/sites-available/$DOMAIN"

say()  { printf '\n\033[1;36m==> %s\033[0m\n' "$*"; }
fail() { printf '\n\033[1;31mERRO: %s\033[0m\n' "$*" >&2; exit 1; }

[ "$(id -u)" -eq 0 ] || fail "Rode como root."
command -v certbot >/dev/null || fail "certbot não instalado."
[ -f "$VHOST" ] || fail "vhost $VHOST não existe (rode deploy/install.sh antes)."
[ -f "$APP_DIR/deploy/nginx.ssl.tpl" ] || fail "Falta $APP_DIR/deploy/nginx.ssl.tpl (rode deploy/update.sh antes)."

say "1/4 Emitindo o certificado para $DOMAIN (o domínio precisa apontar para esta VPS)"
if [ -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]; then
  echo "Certificado já existe: reaproveitando."
else
  certbot certonly --webroot -w "$APP_DIR/public" -d "$DOMAIN" \
    --non-interactive --agree-tos --no-eff-email -m "$EMAIL" \
    --deploy-hook "systemctl reload nginx"
fi

say "2/4 Gerando o vhost HTTPS"
PHPSOCK="$(grep -oE 'unix:[^;]+' "$VHOST" | head -1 | sed 's/^unix://')"
[ -n "$PHPSOCK" ] || PHPSOCK="$(ls /run/php/php*-fpm.sock | sort -V | tail -1)"
cp -a "$VHOST" "$VHOST.bak-http"
sed -e "s#__DOMAIN__#$DOMAIN#g" -e "s#__ROOT__#$APP_DIR#g" -e "s#__PHPSOCK__#$PHPSOCK#g" \
  "$APP_DIR/deploy/nginx.ssl.tpl" > "$VHOST"

say "3/4 Testando o nginx"
if ! nginx -t; then
  cp -a "$VHOST.bak-http" "$VHOST"
  fail "Configuração inválida: o vhost HTTP anterior foi restaurado."
fi
systemctl reload nginx

say "4/4 Verificando"
code="$(curl -s -o /dev/null -w '%{http_code}' --resolve "$DOMAIN:443:127.0.0.1" "https://$DOMAIN/health" || true)"
echo "GET https://$DOMAIN/health => $code"
[ "$code" = "200" ] || fail "HTTPS não respondeu 200. Backup do vhost antigo: $VHOST.bak-http"
echo
echo "Pronto. Abra https://$DOMAIN"
echo "A renovação do certificado é automática (certbot)."
