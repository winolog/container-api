.PHONY: up down build test shell composer clean

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build

test:
	docker compose exec php ./vendor/bin/phpunit

shell:
	docker compose exec php bash

composer:
	docker compose exec php composer install

clean:
	docker compose down -v --remove-orphans