FROM nginx:alpine

MAINTAINER Gram <gram7gram@gmail.com>

COPY /etc/letsencrypt /etc/letsencrypt

COPY ./nginx/http.conf /etc/nginx/conf.d/default.conf

COPY ./nginx/https.conf /etc/nginx/conf.d/default-ssl.conf
