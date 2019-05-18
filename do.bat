@echo off
set DEFAULT_TARGET=dev
set target=
set action=%1

if "%1" == "dev" (
    set target=%1
    set action=%2
)
if "%2" == "dev" (
    set target=%2
)
if "%1" == "prod" (
    set target=%1
    set action=%2
)
if "%2" == "prod" (
    set target=%2
)

if "%action%" == "build" goto build
if "%action%" == "start" goto start
if "%action%" == "stop" goto stop
if "%action%" == "restart" goto stop
if "%action%" == "login" goto login
if "%action%" == "--" goto exec
goto help


:build
docker-compose build %target%
goto:eof


:start
docker-compose up -d %target%
goto:eof


:stop
docker-compose down %target%
if "%1" == "restart" goto start
goto:eof


:login
if "%target%" == "" (
    set target=%DEFAULT_TARGET%
)

docker exec -it casa-youless-logger_%target% sh
goto:eof


:exec
set args=%*
set args=%args:~3%

if "%1" == "dev" (
    set args=%args:~4%
)
if "%1" == "prod" (
    set args=%args:~5%
)
if "%target%" == "" (
    set target=%DEFAULT_TARGET%
)

docker exec -it casa-youless-logger_%target% %ARGS%
goto:eof


:help
echo Usage:
echo   do [TARGET] ACTION
echo   do [TARGET] -- COMMAND
echo.
echo Targets:
echo   prod     Local production image
echo   dev      Local development image, default target
echo.
echo Actions:
echo   build    Build Docker image
echo   start    Start Docker container
echo   stop     Stop Docker container
echo   restart  Restart Docker container
echo   login    Log in to running Docker container
echo   --       Execute the command in the Docker container
echo.
