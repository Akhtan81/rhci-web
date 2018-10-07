
docker run -it --rm --name certbot \
            -v "/var/www/rhci-web/public:/var/www/html" \
            -v "/etc/letsencrypt:/etc/letsencrypt" \
            -v "/var/lib/letsencrypt:/var/lib/letsencrypt" \
            certbot/certbot certonly --webroot \
			--webroot-path /var/www/html \
			-d mobilerecycling.net \
			-d admin.mobilerecycling.net \
			-m gram7gram.work@gmail.com
