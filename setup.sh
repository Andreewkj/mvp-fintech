#!/bin/bash

cp .env.example .env
docker compose up -d --build

echo "⏳ Esperando MySQL ficar disponível..."
./docker/wait-for-it.sh mysql:3306 --timeout=10 --strict -- echo "✅ MySQL disponível!"

docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app chmod -R 777 storage bootstrap/cache
