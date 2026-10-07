#!/usr/bin/env bash
# Instala o hook de conversão de compra no WHMCS. Execute como ROOT.
#   ADS_ID=AW-123456789 ADS_LABEL=AW-123456789/abcDEFghi [GA4_ID=G-XXXX] bash deploy/whmcs-hook.sh
# Cria UM arquivo novo em includes/hooks. Não altera nada que já existe. Para desfazer: apague o arquivo.
set -euo pipefail

WHMCS_DIR="${WHMCS_DIR:-/var/www/whmcs}"
SRC="$(cd "$(dirname "$0")" && pwd)/whmcs/midiaserver_conversion.php"
DEST="$WHMCS_DIR/includes/hooks/midiaserver_conversion.php"
fail() { printf '\033[1;31mERRO: %s\033[0m\n' "$*" >&2; exit 1; }

[ "$(id -u)" -eq 0 ] || fail "Rode como root."
[ -d "$WHMCS_DIR/includes/hooks" ] || fail "Não achei $WHMCS_DIR/includes/hooks (é a pasta do WHMCS?)."
[ -f "$SRC" ] || fail "Arquivo de origem não encontrado: $SRC"
[[ "${ADS_ID:-}" =~ ^AW-[0-9]{6,12}$ ]] || fail "Informe ADS_ID no formato AW-123456789."
[[ "${ADS_LABEL:-}" =~ ^AW-[0-9]{6,12}/[A-Za-z0-9_-]{6,40}$ ]] || fail "Informe ADS_LABEL no formato AW-123456789/abcDEFghi."
[[ -z "${GA4_ID:-}" || "${GA4_ID}" =~ ^G-[A-Z0-9]{6,14}$ ]] || fail "GA4_ID inválido (formato G-XXXXXXXXXX)."

[ -f "$DEST" ] && cp -a "$DEST" "$DEST.bak-$(date +%s)"
sed -e "s#__ADS_ID__#$ADS_ID#" -e "s#__ADS_LABEL__#$ADS_LABEL#" -e "s#__GA4_ID__#${GA4_ID:-}#" "$SRC" > "$DEST.tmp"
php -l "$DEST.tmp" >/dev/null || { rm -f "$DEST.tmp"; fail "O arquivo gerado tem erro de sintaxe; nada foi instalado."; }
mv "$DEST.tmp" "$DEST"
# mesmo dono e permissão dos outros hooks
ref="$(ls -1 "$WHMCS_DIR"/includes/hooks/*.php 2>/dev/null | grep -v midiaserver_conversion | head -1 || true)"
if [ -n "$ref" ]; then chown --reference="$ref" "$DEST"; chmod --reference="$ref" "$DEST"; else chown www-data:www-data "$DEST"; chmod 644 "$DEST"; fi
echo "Hook instalado: $DEST"
echo "Teste: faça um pedido de baixo valor, aceite os cookies no portal e confira a conversão 'Compra' no Google Ads (pode levar algumas horas para aparecer)."
echo "Para desfazer: rm $DEST"
