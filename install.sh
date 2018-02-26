#!/bin/bash

docker-compose down

composer install

docker-compose up -d --force-recreate

>&2 echo "Waiting for PostgresSql to run. Please wait....."
sleep 1m
>&2 echo "PGsql started :)"
>&2 echo "Running all phpunit tests now...."

docker pull phpunit/phpunit

docker run -v $(pwd):/app --rm phpunit/phpunit:latest --bootstrap tests/*

>&2 echo "Your application back end is now ready to serve APIs"
#docker exec -it postgres bash