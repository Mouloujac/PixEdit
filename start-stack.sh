#!/bin/bash

cd pixedit-back
docker-compose up -d
sleep 10
./vendor/bin/sail artisan migrate

cd ../pixedit-front
npm start