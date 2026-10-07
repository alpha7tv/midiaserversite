# Mídia Server — portal com HTTPS. Gerado por deploy/enable-ssl.sh
server {
    listen 80;
    listen [::]:80;
    server_name __DOMAIN__;

    # desafio do Let's Encrypt (renovação automática) e redirecionamento para HTTPS
    location ^~ /.well-known/acme-challenge/ { root __ROOT__/public; }
    location / { return 301 https://$host$request_uri; }
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name __DOMAIN__;

    ssl_certificate     /etc/letsencrypt/live/__DOMAIN__/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/__DOMAIN__/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_session_cache shared:SSL_portal:10m;
    ssl_session_timeout 1d;

    root __ROOT__/public;
    index index.php;
    charset utf-8;
    client_max_body_size 4m;

    access_log /var/log/nginx/__DOMAIN__.access.log;
    error_log  /var/log/nginx/__DOMAIN__.error.log;

    gzip on;
    gzip_comp_level 5;
    gzip_min_length 512;
    gzip_vary on;
    gzip_types text/css application/javascript application/json application/xml image/svg+xml text/plain;

    # nada de arquivos ocultos (permite apenas /.well-known para o certificado SSL)
    location ~ /\.(?!well-known) { deny all; }

    # estáticos versionados (?v=) com cache longo
    location ~* \.(?:css|js|woff2|webp|png|jpe?g|svg|ico|avif|webmanifest)$ {
        try_files $uri =404;
        expires 30d;
        add_header Cache-Control "public, max-age=2592000, immutable";
        access_log off;
    }

    location / {
        try_files $uri /index.php?$query_string;
    }

    # só o front controller executa PHP
    location = /index.php {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index.php;
        fastcgi_pass unix:__PHPSOCK__;
        fastcgi_read_timeout 30s;
    }
    location ~ \.php$ { return 404; }
}
