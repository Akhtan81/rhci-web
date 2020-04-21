#!/bin/bash

source admin/.env

if [ -z "$DATABASE_NAME" ]; then echo "[-] Missing env variable DATABASE_NAME"; exit 1; fi;

if [ -z "$UPLOAD_IMAGE_DIR" ]; then echo "[-] Missing env variable UPLOAD_IMAGE_DIR"; exit 1; fi;

# Create containers
docker-compose build

# Boot containers
docker-compose up -d

# Install dependencies
./php ./composer.phar install

# Create poster upload directory
docker-compose exec admin sh -c "mkdir -p /var/www/html/public$UPLOAD_IMAGE_DIR && chmod 777 /var/www/html/public$UPLOAD_IMAGE_DIR"

bash ./cache

# Update database schema with new configuration
./php bin/console doctrine:migrations:migrate -n

# Insert default data into database
docker-compose exec db psql -U postgres -d $DATABASE_NAME -a -f /var/www/html/admin/provision.sql

cat boot/cron/backup.conf | crontab -u root -
