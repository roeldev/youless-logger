# local.image (without tag)
IMAGE_NAME= roeldev/casa-youless-logger
# local.container_name
CONTAINER_NAME=casa-youless-logger
# local.build.args.PHP_VERSION
DEFAULT_PHP_VERSION=7.3

it: build start-dev

dev: build start-dev login-dev

build:
	docker-compose build local
	docker-compose build dev

start:
	docker-compose up local

start-dev:
	docker-compose up dev

stop:
	docker stop ${CONTAINER_NAME} ${CONTAINER_NAME}_dev

kill: stop
	docker rm ${CONTAINER_NAME} ${CONTAINER_NAME}_dev

restart: stop start

restart: stop start-dev

inspect:
	docker inspect ${IMAGE_NAME}:local

inspect-dev:
	docker inspect ${IMAGE_NAME}:dev

login:
	docker exec -it ${CONTAINER_NAME} bash

login-dev:
	docker exec -it ${CONTAINER_NAME}_dev bash

renew:
	docker pull roeldev/php-composer:${DEFAULT_PHP_VERSION}-v1.5
	docker pull roeldev/php-nginx:${DEFAULT_PHP_VERSION}-v1

.PHONY it build start start-dev stop kill restart restart-dev inspect inspect-dev login login-dev renew:
