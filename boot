#!/bin/sh

docker-compose up -d && \
docker-compose exec -u 1000 php8 composer install