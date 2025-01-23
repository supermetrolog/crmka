PHP_CONTAINER_NAME=php

ps:
	docker-compose ps

up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

restart: down up

exec\:php:
	docker-compose exec $(PHP_CONTAINER_NAME) $(ARGV)

exec\:php\:bash:
	make exec:php ARGV=bash
