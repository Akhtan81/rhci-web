#!/bin/bash

source admin/.env

# Create containers
docker-compose build

# Boot containers
docker-compose up -d

# Install dependencies
./php ./composer.phar install

# Create database if not exists
./php bin/console doctrine:database:create --if-not-exists

# Create poster upload directory
docker-compose exec admin sh -c "mkdir -p /var/www/html/public$UPLOAD_IMAGE_DIR && chmod 777 /var/www/html/public$UPLOAD_IMAGE_DIR"

./update