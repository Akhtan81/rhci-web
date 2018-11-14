PROJECT_DIR=/var/www/rhci-web

docker run -it --rm --name certbot \
            -v "$PROJECT_DIR/admin/public:/var/www/html" \
            -v "/etc/letsencrypt:/etc/letsencrypt" \
            -v "/var/lib/letsencrypt:/var/lib/letsencrypt" \
            certbot/certbot certonly --webroot \
			--webroot-path /var/www/html \
			-d mobilerecycling.net \
			-d admin.mobilerecycling.net \
			-m gram7gram.work@gmail.com

cp $PROJECT_DIR/admin/public/.well-known/acme-challenge/* \
    $PROJECT_DIR/www/public/.well-known/acme-challenge
