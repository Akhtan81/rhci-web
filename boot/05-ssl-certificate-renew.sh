
# OPTIONAL. SSL CERTIFICATES EXPIRE IN 3-6 MONTH WITHOUT RENEW

PROJECT_DIR=/var/www/rhci-web

docker run --rm --name certbot \
            -v "$PROJECT_DIR/admin/public:/var/www/html" \
            -v "$PROJECT_DIR/www/public:/usr/share/nginx/html/public" \
            -v "/etc/letsencrypt:/etc/letsencrypt" \
            -v "/var/lib/letsencrypt:/var/lib/letsencrypt" \
            certbot/certbot renew
