-include .env

NO_COLOR=$(shell tput sgr0 -T xterm)
RED=$(shell tput bold -T xterm)$(shell tput setaf 1 -T xterm)
GREEN=$(shell tput bold -T xterm)$(shell tput setaf 2 -T xterm)
YELLOW=$(shell tput bold -T xterm)$(shell tput setaf 3 -T xterm)
BLUE=$(shell tput bold -T xterm)$(shell tput setaf 4 -T xterm)

GIT_BRANCH=$(shell git rev-parse --abbrev-ref HEAD)

default: install

.PHONY: install
install:
	@test -f ./.env || (echo ${RED}Please create .env from .env.dist${NO_COLOUR}; exit 1)
	@test -f ./.docker/php.ini || (echo ${RED}Please create .docker/php.ini from .docker/php.ini.dist${NO_COLOUR}; exit 1)
	@test -f ./docker-compose.yml || (echo ${RED}Please create docker-compose.yml from docker-compose.yml.dist${NO_COLOUR}; exit 1)
	@echo '${BLUE}Building docker containers${NO_COLOR}'
	docker-compose up -d --force-recreate --remove-orphans
	@echo '${BLUE}Composer install${NO_COLOR}'
	@docker-compose exec -T app sh -c "composer install --prefer-dist"
	@docker-compose exec -T app sh -c "composer dump-autoload"
	@echo '${BLUE}Migrate database${NO_COLOR}'
	@docker-compose exec -T app sh -c "./bin/console doctrine:migrations:migrate -n"
	@echo '${BLUE}Load fixtures${NO_COLOR}'
	@docker-compose exec -T app sh -c "./bin/console doctrine:fixtures:load -n"

.PHONY: init
init:
	@echo '${BLUE}Setting folder permissions${NO_COLOR}'
	chmod -R 777 var/
	@echo '${GREEN}Destroying caches${NO_COLOR}'
	rm -rf var/cache
	php bin/console -q --env=${APP_ENV}

.PHONY: db
db:
	echo ${APP_ENV}
ifneq (${APP_ENV}, dev)
	$(error ${RED}Do not run unless in the dev environment${NO_COLOUR})
endif
	@echo '${BLUE}Rebuilding Dev Database${NO_COLOR}'
	@mysql -u root -p${DATABASE_ROOT_PASSWORD} -e "DROP DATABASE IF EXISTS ${DATABASE_NAME};"
	@mysql -u root -p${DATABASE_ROOT_PASSWORD} -e "CREATE DATABASE ${DATABASE_NAME};"
	php bin/console doctrine:migrations:migrate -n
	php bin/console doctrine:fixtures:load -n

.PHONY: cache
cache:
	@echo '${BLUE}Rebuilding Cache${NO_COLOR}'
	rm -rf var/cache
	php bin/console -q

.PHONY: test
test:
	@echo '${BLUE}Running PHPUnit Tests${NO_COLOR}'
	./vendor/bin/phpunit tests

