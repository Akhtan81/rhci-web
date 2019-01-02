
# OPTIONAL. SSL CERTIFICATES EXPIRE IN 3-6 MONTH WITHOUT RENEW

PROJECT_DIR=/var/www/rhci-web

docker run --rm --name certbot \
            -v "$PROJECT_DIR/admin/public:/var/www/html" \
            -v "/etc/letsencrypt:/etc/letsencrypt" \
            -v "/var/lib/letsencrypt:/var/lib/letsencrypt" \
            certbot/certbot renew

#docker-compose restart admin www
