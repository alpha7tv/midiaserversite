#!/usr/bin/env bash
# Instala/atualiza o portal Mídia Server (staging) na VPS. Execute como o usuário claudeops, SEM sudo.
# Faz: código (git), .env, banco (migrate + seed), vhost nginx do domínio de teste, teste e recarga do nginx.
# NÃO altera nenhum outro vhost, o WHMCS, o Studio ou outros sistemas.
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/portal-novo}"
DOMAIN="${DOMAIN:-novo.midiaserver.com.br}"
REPO="${REPO:-https://github.com/alpha7tv/midiaserversite.git}"
BRANCH="${BRANCH:-main}"
DB_FILE="${DB_FILE:-$HOME/.portal-db}"
APP_ENV_VALUE="${APP_ENV_VALUE:-staging}"
VHOST="/etc/nginx/sites-available/$DOMAIN"

say()  { printf '\n\033[1;36m==> %s\033[0m\n' "$*"; }
fail() { printf '\n\033[1;31mERRO: %s\033[0m\n' "$*" >&2; exit 1; }

say "1/8 Conferindo requisitos"
[ "$(id -u)" -ne 0 ] || fail "Rode como claudeops, não como root."
command -v git  >/dev/null || fail "git não instalado."
command -v php  >/dev/null || fail "php (CLI) não instalado."
command -v curl >/dev/null || fail "curl não instalado."
php -r 'exit(version_compare(PHP_VERSION, "8.1.0", ">=") ? 0 : 1);' || fail "PHP 8.1 ou superior é necessário (atual: $(php -r 'echo PHP_VERSION;'))."
for ext in pdo_mysql mbstring json; do php -m | grep -qix "$ext" || fail "extensão PHP ausente: $ext"; done
[ -r "$DB_FILE" ] || fail "Arquivo de credenciais do banco não encontrado: $DB_FILE (rode o Passo 1 como root)."
[ -d "$APP_DIR" ] && [ -w "$APP_DIR" ] || fail "Pasta $APP_DIR inexistente ou sem permissão de escrita."
[ -w "$VHOST" ] || [ ! -e "$VHOST" ] || fail "Sem permissão de escrita em $VHOST (rode o Passo 1 como root)."
sudo -n "$(command -v nginx || echo /usr/sbin/nginx)" -t >/dev/null 2>&1 || fail "sudo para 'nginx -t' não liberado para o claudeops."

say "2/8 Código do portal"
if [ -d "$APP_DIR/.git" ]; then
  git -C "$APP_DIR" fetch --depth=1 origin "$BRANCH"
  git -C "$APP_DIR" reset --hard "origin/$BRANCH"
elif [ -f "$APP_DIR/bin/console" ] && [ -f "$APP_DIR/bootstrap.php" ]; then
  echo "Código já presente em $APP_DIR (enviado por arquivo): usando como está."
else
  if [ -n "$(ls -A "$APP_DIR" 2>/dev/null)" ]; then fail "$APP_DIR não está vazia e não contém o portal."; fi
  git clone --depth=1 --branch "$BRANCH" "$REPO" "$APP_DIR"
fi
cd "$APP_DIR"

say "3/8 Configurando o .env"
if [ ! -f .env ]; then
  # shellcheck disable=SC1090
  . "$DB_FILE"
  APP_KEY="$(openssl rand -hex 32)"
  cat > .env <<ENV
APP_ENV=$APP_ENV_VALUE
APP_URL=https://$DOMAIN
APP_DEBUG=0
APP_KEY=$APP_KEY
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_NAME=$DB_NAME
DB_USER=$DB_USER
DB_PASS=$DB_PASS
WHMCS_BASE=https://cliente-area.midiaserver.com.br
WHATSAPP_NUMBER=5514988159045
ENV
  echo ".env criado."
else
  echo ".env já existe, mantido."
fi
chmod 640 .env
chgrp www-data .env 2>/dev/null || true
mkdir -p storage/logs storage/cache
chmod -R g+w storage 2>/dev/null || true

say "4/8 Banco de dados (tabelas e catálogo)"
php bin/console migrate
php bin/console seed

say "5/8 Sitemap"
php bin/console sitemap

say "6/8 Servidor web (nginx) para $DOMAIN"
PHPSOCK=""
PHPV="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
if [ -S "/run/php/php$PHPV-fpm.sock" ]; then PHPSOCK="/run/php/php$PHPV-fpm.sock"; else PHPSOCK="$(ls /run/php/php*-fpm.sock 2>/dev/null | sort -V | tail -1 || true)"; fi
[ -n "$PHPSOCK" ] || fail "Socket do PHP-FPM não encontrado em /run/php/."
echo "PHP-FPM: $PHPSOCK"
if [ -f "$VHOST" ] && grep -q 'ssl_certificate' "$VHOST"; then
  echo "vhost já tem SSL configurado: mantido sem alterações."
else
  sed -e "s#__DOMAIN__#$DOMAIN#g" -e "s#__ALIASES__##g" -e "s#__ROOT__#$APP_DIR#g" -e "s#__PHPSOCK__#$PHPSOCK#g" deploy/nginx.vhost.tpl > "$VHOST"
  [ -e "/etc/nginx/sites-enabled/$DOMAIN" ] || fail "Falta o link /etc/nginx/sites-enabled/$DOMAIN (rode o Passo 1 como root)."
fi

say "7/8 Testando e recarregando o nginx"
NGINX="$(command -v nginx || echo /usr/sbin/nginx)"
sudo -n "$NGINX" -t
sudo -n "$(command -v systemctl || echo /bin/systemctl)" reload nginx

say "8/8 Verificando"
sleep 1
code="$(curl -s -o /dev/null -w '%{http_code}' -H "Host: $DOMAIN" http://127.0.0.1/health || true)"
echo "GET /health => $code"
[ "$code" = "200" ] || fail "O portal não respondeu 200 em /health. Veja /var/log/nginx/$DOMAIN.error.log e $APP_DIR/storage/logs/."
home="$(curl -s -o /dev/null -w '%{http_code}' -H "Host: $DOMAIN" http://127.0.0.1/ || true)"
echo "GET / => $home"

cat <<DONE

Pronto. O portal está no ar em http://$DOMAIN (staging, bloqueado para buscadores).
Próximo passo (como root, uma vez): emitir o certificado SSL:
   certbot --nginx -d $DOMAIN
DONE
