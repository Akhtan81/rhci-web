#!/bin/bash

source .env

# Boot containers
docker-compose up -d
EXIT_CODE=$?; if [[ $EXIT_CODE != 0 ]]; then; exit $EXIT_CODE; fi

# Install dependencies
./php ./composer.phar install
EXIT_CODE=$?; if [[ $EXIT_CODE != 0 ]]; then; exit $EXIT_CODE; fi

# Update project cache
./cache
EXIT_CODE=$?; if [[ $EXIT_CODE != 0 ]]; then; exit $EXIT_CODE; fi

# Create database if not exists
./php bin/console doctrine:database:create --if-not-exists
EXIT_CODE=$?; if [[ $EXIT_CODE != 0 ]]; then; exit $EXIT_CODE; fi

# Create poster upload directory
docker-compose exec app sh -c "mkdir -p /var/www/html/public$UPLOAD_IMAGE_DIR && chmod 777 /var/www/html/public$UPLOAD_IMAGE_DIR"
EXIT_CODE=$?; if [[ $EXIT_CODE != 0 ]]; then; exit $EXIT_CODE; fi

# Update database schema with new configuration
./php bin/console doctrine:migrations:migrate -n
EXIT_CODE=$?; if [[ $EXIT_CODE != 0 ]]; then; exit $EXIT_CODE; fi

# Seed database
./php bin/console mrs:sync-partner-categories
EXIT_CODE=$?; if [[ $EXIT_CODE != 0 ]]; then; exit $EXIT_CODE; fi