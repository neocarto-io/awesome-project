AwesomeProject
---
Rapid development toolkit for Docker/Symfony flavoured projects

## Scope

Make life easy for developers who use docker, docker-compose and symfony flavoured projects using off-the-shelf tools

## Objectives

- [x] aggregate `docker-compose` configuration (configuration conflicts handlings)
- [x] http routing support
- [ ] repository like definitions for incorporated services (git submodule, hardcopy, local, symlink)

## Development setup

```shell
$ git clone https://github.com/romania2x/awesome-project /path/to/awesome-project
$ composer install
$ ln -s /path/to/awesome-project/awesome-project /usr/local/bin/aproject
```

## Setting up an environment

An environment root is marked by the presence of a file called: `awesome-system.yaml`. This file contains all the
settings required by awesome project:

```yaml
projectsRoot: "./services"
projects:
  user-data:
    source: git@github.com:romania2x/user-data.git
  user-management:
    source: git@github.com:romania2x/user-management.git
  data-aggregator:
    source: git@github.com:romania2x/data-aggregator.git
  ng-controlpanel:
    source: git@github.com:romania2x/ng-controlpanel.git
  php-commons:
    source: git@github.com:romania2x/php-commons.git
ports:
  admin_http: 8881
routes:
  kibana:5601:
    - kibana.romania2x/
  mongo-express:8081:
    - mongo-express.romania2x/
  user-management-nginx:
    - terranova.romania2x/api/users
  user-data-nginx:
    - terranova.romania2x/api/user-data
  data-aggregator-nginx:
    - terranova.romania2x/api/global-data
  controlpanel:4200/controlpanel:
    - terranova.romania2x/controlpanel
  controlpanel:4200/sockjs-node:
    - terranova.romania2x/sockjs-node
  mercure/.well-known/mercure:
    - terranova.romania2x/.well-known/mercure
```

To install all your dependent subprojects, execute `aproject install [-vvv]`.

To start everything up, execute: `aproject up [-vvv]` in the root of your awesome project.

Now the HTTP gateway will listen on port 80 of your local ip.

To see the available arguments, execute:

```shell

$ aproject     
AwesomeProject 0.1.0

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  down     Smooth shutdown
  help     Displays help for a command
  install  Install services
  kill     Kill all processes as fast as possible
  list     Lists commands
  restart  Recompule/Restart the cofiguration
  up       Start the configuration

```
