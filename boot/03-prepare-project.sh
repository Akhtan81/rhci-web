#!/bin/bash

source admin/.env

# Create containers
docker-compose build
EXIT_CODE=$?; if [[ $EXIT_CODE != 0 ]]; then; exit $EXIT_CODE; fi

# Boot containers
docker-compose up -d
EXIT_CODE=$?; if [[ $EXIT_CODE != 0 ]]; then; exit $EXIT_CODE; fi

# Install dependencies
./php ./composer.phar install
EXIT_CODE=$?; if [[ $EXIT_CODE != 0 ]]; then; exit $EXIT_CODE; fi

# Create database if not exists
./php bin/console doctrine:database:create --if-not-exists
EXIT_CODE=$?; if [[ $EXIT_CODE != 0 ]]; then; exit $EXIT_CODE; fi

# Create poster upload directory
docker-compose exec admin sh -c "mkdir -p /var/www/html/public$UPLOAD_IMAGE_DIR && chmod 777 /var/www/html/public$UPLOAD_IMAGE_DIR"
EXIT_CODE=$?; if [[ $EXIT_CODE != 0 ]]; then; exit $EXIT_CODE; fi

./update