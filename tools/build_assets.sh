#!/usr/bin/env bash
# Gera public/assets/{css,js}/app.min.* com esbuild e grava o hash dos fontes (conferido pelo teste de fumaça).
# Rode sempre que alterar app.css ou app.js:  bash tools/build_assets.sh
set -euo pipefail
cd "$(dirname "$0")/.."
ESB="${ESBUILD:-npx --yes esbuild@0.28.2}"
$ESB public/assets/css/app.css --minify --outfile=public/assets/css/app.min.css --log-level=warning
$ESB public/assets/js/app.js --minify --target=es2017 --outfile=public/assets/js/app.min.js --log-level=warning
printf '{"css":"%s","js":"%s"}\n' "$(sha1sum public/assets/css/app.css | cut -d' ' -f1)" "$(sha1sum public/assets/js/app.js | cut -d' ' -f1)" > public/assets/.build.json
ls -l public/assets/css/app.css public/assets/css/app.min.css public/assets/js/app.js public/assets/js/app.min.js | awk '{print $5, $9}'
