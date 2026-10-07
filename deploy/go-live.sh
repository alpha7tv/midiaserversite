#!/usr/bin/env bash
# Publica o portal em produção no domínio principal (nginx + HTTPS Let's Encrypt + modo produção).
# Execute como ROOT. Só cria/altera o vhost deste domínio. Reexecutável.
#
#   bash deploy/go-live.sh
#   DOMAIN=midiaserver.com.br ALIASES="www.midiaserver.com.br novo.midiaserver.com.br" bash deploy/go-live.sh
set -euo pipefail

DOMAIN="${DOMAIN:-midiaserver.com.br}"
ALIASES="${ALIASES:-www.midiaserver.com.br novo.midiaserver.com.br}"
APP_DIR="${APP_DIR:-/var/www/portal-novo}"
EMAIL="${EMAIL:-contato@midiaserver.com.br}"
RUN_AS="${RUN_AS:-claudeops}"
VHOST="/etc/nginx/sites-available/$DOMAIN"

say()  { printf '\n\033[1;36m==> %s\033[0m\n' "$*"; }
warn() { printf '\033[1;33mAVISO: %s\033[0m\n' "$*"; }
fail() { printf '\n\033[1;31mERRO: %s\033[0m\n' "$*" >&2; exit 1; }

[ "$(id -u)" -eq 0 ] || fail "Rode como root."
command -v certbot >/dev/null || fail "certbot não instalado."
command -v nginx   >/dev/null || fail "nginx não instalado."
[ -f "$APP_DIR/bootstrap.php" ] || fail "Portal não encontrado em $APP_DIR."
[ -f "$APP_DIR/deploy/nginx.ssl.tpl" ] || fail "Faltam os templates em $APP_DIR/deploy (rode: su - $RUN_AS -c 'bash $APP_DIR/deploy/update.sh')."

say "1/8 Diagnóstico do servidor"
echo "Portas escutando (80 e 443):"
ss -tlnp 2>/dev/null | grep -E ':(80|443)\s' | awk '{print "  " $4 "  " $NF}' || true
if command -v ufw >/dev/null 2>&1; then echo "ufw: $(ufw status 2>/dev/null | head -1)"; fi
PUBIP="$(ip -4 -o addr show scope global 2>/dev/null | awk '{print $4}' | cut -d/ -f1 | head -1)"
echo "IP da VPS: ${PUBIP:-desconhecido}"
for d in $DOMAIN $ALIASES; do
  r="$(getent ahostsv4 "$d" 2>/dev/null | awk '{print $1; exit}')"
  printf '  DNS %-34s -> %s\n' "$d" "${r:-(sem resposta)}"
  if [ -n "${PUBIP:-}" ] && [ "$r" != "$PUBIP" ]; then warn "$d não aponta para $PUBIP. O certificado só sai se apontar."; fi
done

say "2/8 Conferindo conflito com outros sites no nginx"
OTHERS="$(grep -rlE "server_name[^;]*[[:space:]]$DOMAIN([[:space:];])" /etc/nginx/sites-enabled /etc/nginx/conf.d 2>/dev/null | grep -v "/$DOMAIN\$" | grep -v "/novo.$DOMAIN\$" || true)"
if [ -n "$OTHERS" ]; then
  echo "$OTHERS"
  fail "Já existe outro arquivo do nginx usando o nome $DOMAIN (listado acima). Veja o conteúdo antes de continuar; não vou sobrescrever."
fi
echo "Sem conflitos."

PHPSOCK="$(grep -oE 'unix:[^;]+' "/etc/nginx/sites-available/novo.$DOMAIN" 2>/dev/null | head -1 | sed 's/^unix://')"
[ -n "$PHPSOCK" ] || PHPSOCK="$(ls /run/php/php*-fpm.sock 2>/dev/null | sort -V | tail -1 || true)"
[ -n "$PHPSOCK" ] || fail "Socket do PHP-FPM não encontrado."
echo "PHP-FPM: $PHPSOCK"

say "3/8 vhost HTTP (necessário para o desafio do certificado)"
# o vhost antigo do staging (novo.*) passa a ser coberto por este; desativa para não duplicar o nome
if [ -L "/etc/nginx/sites-enabled/novo.$DOMAIN" ]; then rm -f "/etc/nginx/sites-enabled/novo.$DOMAIN"; echo "vhost antigo novo.$DOMAIN desativado (arquivo mantido em sites-available)."; fi
[ -f "$VHOST" ] && cp -a "$VHOST" "$VHOST.bak-$(date +%s)"
sed -e "s#__DOMAIN__#$DOMAIN#g" -e "s#__ALIASES__#$ALIASES#g" -e "s#__ROOT__#$APP_DIR#g" -e "s#__PHPSOCK__#$PHPSOCK#g" \
  "$APP_DIR/deploy/nginx.vhost.tpl" > "$VHOST"
ln -sf "$VHOST" "/etc/nginx/sites-enabled/$DOMAIN"
nginx -t || fail "nginx -t falhou com o vhost HTTP."
systemctl reload nginx

say "4/8 Certificado SSL (Let's Encrypt)"
ARGS=(-d "$DOMAIN"); for a in $ALIASES; do ARGS+=(-d "$a"); done
if [ -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]; then
  echo "Certificado já existe: reaproveitando (para incluir novos nomes: apague /etc/letsencrypt/live/$DOMAIN e rode de novo)."
else
  certbot certonly --webroot -w "$APP_DIR/public" "${ARGS[@]}" \
    --non-interactive --agree-tos --no-eff-email -m "$EMAIL" --deploy-hook "systemctl reload nginx" \
    || fail "O certbot não conseguiu emitir o certificado. Confira o DNS (etapa 1) e se as portas 80/443 estão liberadas no firewall da Contabo."
fi

say "5/8 vhost HTTPS"
sed -e "s#__DOMAIN__#$DOMAIN#g" -e "s#__ALIASES__#$ALIASES#g" -e "s#__ROOT__#$APP_DIR#g" -e "s#__PHPSOCK__#$PHPSOCK#g" \
  "$APP_DIR/deploy/nginx.ssl.tpl" > "$VHOST.new"
[ -n "$ALIASES" ] || sed -i '/# ALIAS-BEGIN/,/# ALIAS-END/d' "$VHOST.new"
cp -a "$VHOST" "$VHOST.bak-http"
mv "$VHOST.new" "$VHOST"
if ! nginx -t; then cp -a "$VHOST.bak-http" "$VHOST"; fail "Configuração HTTPS inválida; o vhost HTTP foi restaurado."; fi
systemctl reload nginx

say "6/8 Modo produção"
cd "$APP_DIR"
if [ -f .env ]; then
  sed -i -e 's#^APP_ENV=.*#APP_ENV=production#' -e "s#^APP_URL=.*#APP_URL=https://$DOMAIN#" .env
else
  fail ".env não encontrado em $APP_DIR (rode deploy/install.sh)."
fi
chown "$RUN_AS":www-data .env; chmod 640 .env
su - "$RUN_AS" -c "cd $APP_DIR && php bin/console sitemap" || warn "não consegui gerar o sitemap agora."

say "7/8 Verificando"
for path in /health / /hospedagem /streaming /automacao-radio /robots.txt /sitemap.xml; do
  code="$(curl -s -o /dev/null -w '%{http_code}' --resolve "$DOMAIN:443:127.0.0.1" "https://$DOMAIN$path" || true)"
  printf '  https://%s%-22s => %s\n' "$DOMAIN" "$path" "$code"
  [ "$code" = "200" ] || fail "Esperava 200 em $path."
done
www="$(curl -s -o /dev/null -w '%{http_code} %{redirect_url}' --resolve "www.$DOMAIN:443:127.0.0.1" "https://www.$DOMAIN/" || true)"
echo "  https://www.$DOMAIN/ => $www"
robots="$(curl -s --resolve "$DOMAIN:443:127.0.0.1" "https://$DOMAIN/robots.txt" | head -2 | tr '\n' ' ')"
echo "  robots.txt: $robots"

say "8/8 Pronto"
echo "Portal no ar: https://$DOMAIN  (www e novo redirecionam para o endereço principal)"
echo "Backup do vhost anterior: ${VHOST}.bak-*"
