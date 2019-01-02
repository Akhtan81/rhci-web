PROJECT_DIR=/var/www/rhci-web
OWNER=gram7gram.work@gmail.com

sudo docker run -it --rm --name certbot \
            -v "$PROJECT_DIR/www/public:/usr/share/nginx/html/public" \
            -v "/etc/letsencrypt:/etc/letsencrypt" \
            -v "/var/lib/letsencrypt:/var/lib/letsencrypt" \
            certbot/certbot certonly --webroot --webroot-path /usr/share/nginx/html/public \
            -d mobilerecycling.net \
            -m $OWNER

sudo docker run -it --rm --name certbot \
            -v "$PROJECT_DIR/admin/public:/var/www/html" \
            -v "/etc/letsencrypt:/etc/letsencrypt" \
            -v "/var/lib/letsencrypt:/var/lib/letsencrypt" \
            certbot/certbot certonly --webroot --webroot-path /var/www/html \
            -d admin.mobilerecycling.net \
            -m $OWNER

cat $PROJECT_DIR/boot/cron/ssl-renew.conf | crontab -u root -
