.PHONY: init build migrate help

define echo_green
	@printf "\n  \e[30;42m %s \033[0m\n\n" '$(1)'
endef

default: help

init: prepare build clean
	@echo "--> Установка зависимостей Composer..."
	@make composer
	@echo "--> Ожидание инициализации базы данных (10 секунд)..."
	@sleep 10
	@echo "--> Создание и применение миграций БД..."
	@make migrate
	@echo "✅ Проект успешно установлен и запущен!"
	@echo "API доступно по адресу: http://localhost:8000"

prepare:
	@$(call echo_green, "Prepare environment")
	@if [ ! -f ./.env ]; then cp ./.env.example ./.env; fi;
	@if [ ! -f ./src/.env ]; then cp ./src/.env.example ./src/.env; fi;
	@mkdir -p ./src/var/cache ./src/var/log
	@chmod -R 777 ./src/var/cache ./src/var/log

clean:
	@$(call echo_green, "Clean cache and logs")
	@rm -rf ./src/var/cache/* ./src/var/log/*

composer:
	@$(call echo_green, "Composer install")
	@docker compose exec php composer install

build:
	@$(call echo_green, "Docker build and up")
	@docker compose up -d --remove-orphans --force-recreate --build

schedules:
	@docker compose exec php php bin/console debug:scheduler

migrate:
	@docker compose exec php php bin/console make:migration --no-interaction
	@docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

checks:
	@docker compose exec php php bin/console doctrine:schema:validate
	@docker compose exec php php bin/console lint:yaml config/
	@docker compose exec php php bin/console lint:container
	@docker compose exec php composer phpstan
	@docker compose exec php composer phpcs
	@docker compose exec php composer php-cs-fixer
	@$(call echo_green, "All checks passed!")