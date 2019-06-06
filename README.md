Logger service for YouLess energy monitor
=========================================

[![Latest release][latest-release-img]][latest-release-url]
[![Build status][build-status-img]][build-status-url]
[![Layers][image-layers-img]][image-layers-url]
[![Image size][image-size-img]][image-size-url]
[![Code maintainability][maintainability-img]][maintainability-url]

[latest-release-img]: https://img.shields.io/github/release/project-casa/youless-logger.svg?label=latest
[latest-release-url]: https://github.com/project-casa/youless-logger/releases
[build-status-img]: https://img.shields.io/docker/cloud/build/roeldev/casa-youless-logger.svg
[build-status-url]: https://hub.docker.com/r/roeldev/casa-youless-logger/builds
[image-layers-img]: https://img.shields.io/microbadger/layers/roeldev/casa-youless-logger/latest.svg
[image-layers-url]: https://microbadger.com/images/roeldev/casa-youless-logger
[image-size-img]: https://img.shields.io/microbadger/image-size/roeldev/casa-youless-logger/latest.svg
[image-size-url]: https://hub.docker.com/r/roeldev/casa-youless-logger/tags
[maintainability-img]: https://img.shields.io/codeclimate/maintainability-percentage/project-casa/youless-logger.svg
[maintainability-url]: https://codeclimate.com/github/project-casa/youless-logger


A _YouLess energy monitor_ is a handy to device to monitor your power and gas usage. It has it's downsides though, one is a limited amount of historical data that is stored. This service aims to solve that problem by storing the monitored data of each interval for a much longer period of time. And does so for multiple YouLess devices. It also provides an API compatible with YouLess' own API so the service can easily be used by any existing client.

Key features are:
- no more limits on monitored data
- get data from multiple devices
- support older YouLess versions
- only monitor the data types you want
- configure update intervals
- cli to instant trigger an update
- classic api can serve older data


## Installation
```docker pull roeldev/casa-youless-logger```


### Docker-compose
```
services:
  youless-logger-service:
    image: roeldev/casa-youless-logger:latest
    volumes:
      - config:/youless-logger/config/
      - data:/youless-logger/data/
      - log:/youless-logger/log/
```


### Volumes
| Path | Contains |
|------|----------|
|```/youless-logger/config```| Config files
|```/youless-logger/data```| Database (backup) files
|```/youless-logger/log```| Log files


## Configuration
Support for multiple YouLess devices means a little more configuration. Altough it should be pretty self explanatory, most important stuff is mentioned in this chapter. But first, have a look at the _[sample config](youless-logger/config/config-example.php)_ for a quick overview of possibilities.

Each device should have at least a _host_ option set so the logger can reach the device's API. All other values are not required. However this probably results in way too much useless data being saved. Therefore it's recommended to only enable the data type(s) that are actually monitored. and disables classic API support
> **Note:** Latest YouLess devices support data from three different counters: power, gas and s0. Older versions only support power.

Below example adds two devices:
1. _house_ which requires a password to access the API on _192.168.1.27_ and provides data of power and gas usage;
2. _shed_ which is hosted on _192.168.1.32_ and only provides data of the s0 pulse counter.

```
'devices' => [
    'house' => [
        'host' => 'http://192.168.1.27',
        'password' => 'secret',
        'update' => [
            'power' => true,
            'gas' => true,
            's0' => false,
        ]
    ],
    'shed' => [
        'host' => 'http://192.168.1.32'
        'update' => [ 's0' ]
    ]
]
```


## CLI
A CLI tool is available to display info or communicate with the configured devices. Login to the Docker container and use `yl list` to list all available commands. It is also possible to run the tool outside of the Docker container via `docker exec -it {container_name} yl {command}`.


## Stored data
A new SQLite database is created on first run. All data values are stored in the `data` table whitin this database.


### Intervals
| Id | Interval | API equiv. | Unit (power/s0) | Unit (gas) |
|----|-----------|------------|-----------------|------------|
| 1 | minute | h | watt | n/a
| 2 | 10 mins | w | watt | liter
| 3 | hour | d | watt | liter
| 4 | day | m | kWh | m3 (cubic meter)

### Units
| Id | Unit | API equiv. | Type |
|----|------|------------|------|
| 1 | watt | Watt | power/s0
| 2 | kwh | kWh | power/s0
| 3 | liter | l | gas
| 4 | m3 | m3 | gas



## Links
- Github: https://github.com/project-casa/youless-logger
- Docker hub: https://hub.docker.com/r/roeldev/casa-youless-logger
- YouLess: https://youless.nl
- YouLess API info: http://wiki.td-er.nl/index.php?title=YouLess


## License
[GPL-2.0+](LICENSE) © 2019 [Roel Schut](https://roelschut.nl)
