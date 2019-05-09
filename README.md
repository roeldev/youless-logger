Logger service for YouLess energy monitor
=========================================

[![Latest release][latest-release-img]][latest-release-url]
[![Build status][build-status-img]][build-status-url]
[![Layers][image-layers-img]][image-layers-url]
![Image size][image-size-img]

[latest-release-img]: https://img.shields.io/github/release/project-casa/youless-logger.svg?label=latest
[latest-release-url]: https://github.com/project-casa/youless-logger/releases
[build-status-img]: https://img.shields.io/docker/cloud/build/roeldev/casa-youless-logger.svg
[build-status-url]: https://hub.docker.com/r/roeldev/casa-youless-logger/builds
[image-layers-img]: https://img.shields.io/microbadger/layers/roeldev/casa-youless-logger/latest.svg
[image-layers-url]: https://microbadger.com/images/roeldev/casa-youless-logger
[image-size-img]: https://img.shields.io/microbadger/image-size/roeldev/casa-youless-logger/latest.svg


By default a YouLess energy monitor only stores data for about a year. This Docker image aims to store the monitored data for a much longer time. In the future an API compatible with YouLess' own API will provide the same data so any existing clients should work pretty much out of the box.


### Volumes
| Path | Contains |
|------|----------|
|```/youless/data```| Stored data
|```/youless/log```| Log files

### Environment variables
| Env. variable | Description |
|---------------|-------------|
|```YOULESS_HOST```| YouLess' host, eg. http://youless
|```YOULESS_PASSWORD```| YouLess access password
|```DB_USERNAME```| Database username
|```DB_PASSWORD```| Database password


## Links
- Github: https://github.com/project-casa/youless-logger
- Docker hub: https://hub.docker.com/r/roeldev/casa-youless-logger
- YouLess: https://youless.nl
- YouLess API info: http://wiki.td-er.nl/index.php?title=YouLess


## License
[GPL-2.0+](LICENSE) © 2019 [Roel Schut](https://roelschut.nl)
