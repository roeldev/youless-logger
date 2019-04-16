@echo off
set DEFAULT_TARGET=dev
set IMAGE=roeldev/casa-youless
set target=
set container=casa-youless
set version=latest

if "%1" == "build" ( goto build )
if "%1" == "start" ( goto start )
if "%1" == "stop" ( goto stop )
if "%1" == "restart" ( goto stop )
if "%1" == "login" ( goto login )
if "%1" == "--" ( goto exec )
if "%2" == "--" ( goto exec )
goto help

:init
if not "%target%" == "" goto:eof

set target=%DEFAULT_TARGET%
if "%1" == "dev" (
    set target=%1
)
if "%1" == "prod" (
    set target=%1
)

if "%target%" == "dev" (
    set container=%container%-dev
    set version=%version%-dev
)
goto:eof

:build
call :init %2

if "%target%" == "dev" (
    set type=DEVELOPMENT
    set dockerfile=Dockerfile.dev
) else (
    set type=PRODUCTION
    set dockerfile=Dockerfile
)

echo Building %type% image from `%dockerfile%` as `%IMAGE%:%version%`

docker build ^
    --file %~dp0docker\%dockerfile% ^
    --force-rm ^
    --tag %IMAGE%:%version% ^
    .
goto:eof

:start
call :init %2
set dir=%~dp0

if "%target%" == "dev" (
    set dirCache=%dir%.composer-cache
    if not exist "%dirCache%\" (
        mkdir %dirCache%
    )

    set volumes=-v "%dir%\youless:/youless/" ^
                -v "%dirCache%:/root/.composer/"
) else (
    set volumes=-v "%dir%\youless\data:/youless/data/" ^
                -v "%dir%\youless\log:/youless/log/"
)

echo Starting `%container%` from image `%IMAGE%:%version%`...

docker run ^
    --detach ^
    --name %container% ^
    %volumes% ^
    %IMAGE%:%version%
goto:eof

:stop
call :init %2
docker stop %container%
docker rm %container%
if "%1" == "restart" ( goto start )
goto:eof

:login
call :init %2
echo Logging in to `%container%`...
echo.

docker exec -it %container% sh
goto:eof

:exec
call :init %1

set args=%*
if not "%2" == "--" (
    set args=%args:~3%
) else (
    if "%target%" == "dev" (
        set args=%args:~7%
    )
    if "%target%" == "prod" (
        set args=%args:~8%
    )
)

docker exec -it %container% %args%
goto:eof

:help
echo Usage:
echo   do action [target]
echo   do [target] -- command
echo.
echo Actions:
echo   build    Build Docker image
echo   start    Start Docker container
echo   stop     Stop Docker container
echo   restart  Restart Docker container
echo   login    Log in to running Docker container
echo   --       Execute the command in the Docker container
echo.
echo Target:
echo   prod     Production container
echo   dev      Development container, default target
echo.
