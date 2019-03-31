YouLess energy monitor
=======================

By default the YouLess energy monitor only stores data for about a year. This Docker image aims to provide the 
monitored data for a much longer time. In the future a (compatible) API should provide the same data as the YouLess device so any existing clients should work out of the box.

### Volumes
| Path | Contains |
|------|----------|
|```/youless/data```  | Stored data
|```/youless/log```   | Log files

### Environment variables
| Env. variable | Description |
|---------------|-------------|
|```YOULES_PASSWORD```| YouLess access password
|```DB_USERNAME```| Database username
|```DB_PASSWORD```| Database password

## Links
- Github: https://github.com/project-casa/youless
- Docker hub: https://hub.docker.com/r/roeldev/casa-youless
- YouLess: https://youless.nl
- YouLess API info: http://wiki.td-er.nl/index.php?title=YouLess
