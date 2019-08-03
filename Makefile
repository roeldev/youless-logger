# local.image (without tag)
IMAGE_NAME= roeldev/casa-youless-logger
# local.container_name
CONTAINER_NAME=casa-youless-logger
# local.build.args.PHP_VERSION
DEFAULT_PHP_VERSION=7.3

.PHONY it:
it: build start-dev

.PHONY build:
build:
	docker-compose build local
	docker-compose build dev

.PHONY start:
start:
	docker-compose up local

.PHONY start-dev:
start-dev:
	docker-compose up dev

.PHONY stop:
stop:
	docker stop ${CONTAINER_NAME} ${CONTAINER_NAME}_dev

.PHONY kill:
kill: stop
	docker rm ${CONTAINER_NAME} ${CONTAINER_NAME}_dev

.PHONY restart:
restart: stop start

.PHONY restart-dev:
restart: stop start-dev

.PHONY inspect:
inspect:
	docker inspect ${IMAGE_NAME}:local

.PHONY inspect-dev:
inspect-dev:
	docker inspect ${IMAGE_NAME}:dev

.PHONY login:
login:
	docker exec -it ${CONTAINER_NAME} bash

.PHONY login-dev:
login-dev:
	docker exec -it ${CONTAINER_NAME}_dev bash

.PHONY renew:
renew:
	docker pull roeldev/php-composer:${DEFAULT_PHP_VERSION}-v1.4
	docker pull roeldev/php-nginx:${DEFAULT_PHP_VERSION}-v1
