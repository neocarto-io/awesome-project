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

1. Clone this repo into your workspace (eg: `git clone https://github.com/romania2x/awesome-project /workspace/awesome-project`)
2. Install the composer dependencies: `composer install`
3. Use local binary: `ln -s /workspace/awesome-project/awesome-project /usr/local/bin/awesome-project`

## Setting up an environment

An environment root is marked by the presence of a file called: `awesome-system.json`. 
This file contains all the settings required by awesome project:
```json
{
  "projectsRoot": "./services",
  "routes": {
    "homepage": {
      "hosts": [
        "terranova.romania2x"
      ],
      "paths": [
        "/"
      ],
      "target": "http://homepage"
    }
  }
}
```

