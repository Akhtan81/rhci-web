PROJECT_DIR=/var/www/rhci-web

docker run -it --rm --name certbot \
            -v "$PROJECT_DIR/admin/public:/var/www/html" \
            -v "/etc/letsencrypt:/etc/letsencrypt" \
            -v "/var/lib/letsencrypt:/var/lib/letsencrypt" \
            certbot/certbot renew

cp $PROJECT_DIR/admin/public/.well-known/acme-challenge/* \
    $PROJECT_DIR/www/public/.well-known/acme-challenge

docker-compose restart app
