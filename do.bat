@echo off
set DEFAULT_TARGET=dev
set IMAGE=roeldev/casa-youless
set target=
set container=casa-youless
set version=latest

:top
if "%1" == "build" goto build
if "%1" == "start" goto start
if "%1" == "stop" goto stop
if "%1" == "restart" goto stop
if "%1" == "login" goto login
if "%1" == "--" goto exec
goto help

:init
set target=%DEFAULT_TARGET%
if "%2" == "dev" set target=dev
if "%2" == "prod" set target=prod

if "%target%" == "dev" set container=%container%-dev
if "%target%" == "dev" set version=%version%-dev
goto top

:build
if "%target%" == "" goto init
docker build ^
    --force-rm ^
    --tag %IMAGE%:%version% ^
    --target %target% ^
    .
goto:eof

:start
if "%target%" == "" goto init

set dir=%~dp0
set volumes=
if "%target%" == "dev" set volumes=-v "%dir%youless:/youless/" -v "%dir%.composer-cache:/root/.composer/"
if "%target%" == "prod" set volumes=-v "%dir%youless\data:/youless/data/" -v "%dir%youless\log:/youless/log/"

docker run --detach --name %container% %volumes% %IMAGE%:%version%
goto:eof

:stop
if "%target%" == "" goto init
docker stop %container%
docker rm %container%
if "%1" == "restart" goto start
goto:eof

:login
if "%target%" == "" goto init
docker exec -it %container% sh
goto:eof

:exec
set ARGS=%*
set ARGS=%ARGS:~3%
echo docker exec -it %container% %ARGS%
goto:eof

:help
echo Usage:
echo   do [action] [target]
echo   do [target] -- [command]
echo.
echo Actions:
echo   build    Build Docker image
echo   start    Start Docker container
echo   stop     Stop Docker container
echo   restart  Restart Docker container
echo   --       Execute the command in the Docker container
echo.
echo Target:
echo   prod     Production container
echo   dev      Development container, default target
echo.
