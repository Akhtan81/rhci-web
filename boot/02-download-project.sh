#!/bin/bash

cd /var/www

git clone https://github.com/almassapargali/rhci-web --depth=1 --branch=master-cis

cd rhci-web

cp docker-compose.yml.dist docker-compose.yml

cp admin/.env.dist admin/.env

cp admin/env/Dockerfile.dist admin/env/Dockerfile