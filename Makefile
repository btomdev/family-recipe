# Variables
DOCKER = docker
HOST_USER = USER_ID=$(shell id -u) USER_NAME=$(shell whoami) GROUP_ID=$(shell id -g)
DOCKER_COMPOSE = ${HOST_USER} docker-compose
EXEC = $(DOCKER) exec -it php-fpm
PHP = $(EXEC) php
COMPOSER = $(EXEC) composer
NPM = $(EXEC) npm
SYMFONY_CONSOLE = $(PHP) bin/console

## —— 🔥 App —————————————————————————————————————————————————————————————————
init: ## Init the project
	$(MAKE) up-local
	#$(MAKE) up-prod
	$(MAKE) install
	@echo
	@echo "📣 L'app' est disponible !"
	@echo
	@echo "✅ app : \033[32mhttp://localhost:80/home\033[0m"
	@echo "✅ database : \033[32mhttp://localhost:8081\033[0m"
	@echo "✅ maildev : \033[32mhttp://127.0.0.1:8025//\033[0m"

install:
	$(MAKE) database-init
	$(MAKE) composer-install
	$(MAKE) npm-install

start:
	$(MAKE) up-local
	@echo
	@echo "📣 L'app' est disponible !"
	@echo
	@echo "✅ app : \033[32mhttp://localhost:80/home\033[0m"
	@echo "✅ database : \033[32mhttp://localhost:8081\033[0m"
	@echo "✅ maildev : \033[32mhttp://127.0.0.1:8025//\033[0m"

cache-clear: ## Clear cache
	$(SYMFONY_CONSOLE) cache:clear

shell:
	$(EXEC) /bin/bash

console:
	$(PHP) ./bin/console $(COMMAND)

## —— 🐳 Docker —————————————————————————————————————————————————————————————————
up-local:
	$(DOCKER_COMPOSE) --env-file .env.local up --build -d

up-prod:
	$(DOCKER_COMPOSE) --env-file .env.prod up --build -d

stop: ## Stop app
	$(MAKE) docker-stop

docker-stop: 
	$(DOCKER_COMPOSE) stop
	@$(call RED,"The containers are now stopped.")

down-local:
	$(DOCKER_COMPOSE) --env-file .env.local down

clean-up: ## Remove containers, images and volumes
	$(DOCKER_COMPOSE) down --remove-orphans --rmi all --volumes
	rm -rfi ./app
	rm -rfi ./data
	rm -rfi ./logs

## —— 🎻 Composer ——
composer-install: ## Install dependencies
	$(COMPOSER) install

composer-update: ## Update dependencies
	$(COMPOSER) update

composer-clear-cache: ## clear-cache dependencies
	$(COMPOSER) clear-cache

## —— 🐈 NPM —————————————————————————————————————————————————————————————————
npm-install: ## Install all npm dependencies
	$(NPM) init -y
	$(NPM) install

npm-update: ## Update all npm dependencies
	$(NPM) update

npm-watch: ## Update all npm dependencies
	$(NPM) run watch

## —— 📊 Database —————————————————————————————————————————————————————————————————
database-init: ## Init database
	$(MAKE) database-drop
	$(MAKE) database-create
	$(MAKE) database-migrate
	$(MAKE) database-fixtures-load

database-drop: ## Create database
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists

database-create: ## Create database
	$(SYMFONY_CONSOLE) d:d:c --if-not-exists

database-remove: ## Drop database
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists

database-migration: ## Make migration
	$(SYMFONY_CONSOLE) make:migration

migration: ## Alias : database-migration
	$(MAKE) database-migration

database-migrate: ## Migrate migrations
	$(SYMFONY_CONSOLE) d:m:m --no-interaction

migrate: ## Alias : database-migrate
	$(MAKE) database-migrate

database-fixtures-load: ## Load fixtures
	$(SYMFONY_CONSOLE) d:f:l --no-interaction

fixtures: ## Alias : database-fixtures-load
	$(MAKE) database-fixtures-load

## —— ✅ Test —————————————————————————————————————————————————————————————————
.PHONY: tests
tests: ## Run all tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Unit/
	$(PHP) bin/phpunit --testdox tests/Functional/
	$(PHP) bin/phpunit --testdox tests/E2E/

database-init-test: ## Init database for test
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists --env=test
	$(SYMFONY_CONSOLE) d:d:c --env=test
	$(SYMFONY_CONSOLE) d:m:m --no-interaction --env=test
	$(SYMFONY_CONSOLE) d:f:l --no-interaction --env=test

unit-test: ## Run unit tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Unit/

functional-test: ## Run functional tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Functional/

# PANTHER_NO_HEADLESS=1 ./bin/phpunit --filter LikeTest --debug to debug with Chrome
e2e-test: ## Run E2E tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/E2E/

## —— 🛠️  Others —————————————————————————————————————————————————————————————————
help: ## List of commands
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'