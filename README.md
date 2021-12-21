# Snowtricks

Collaborative web site, about snowboard tricks

* * *

## Table of contents

-   [Installation](#installation)
-   [Development environment](#development-environment)
-   [Use doctrine](#use-doctrine)

* * *

## Installation

Install your project in a new folder, for example `snowtricks`.

```bash
mkdir snowtricks

cd snowtricks

git clone ...

composer install
```

`snowtricks` folder will be your symfony working directory.

You will be able to access following pages, after launching you [development environment](#development-environment):

| Page              | Address        |
| :---------------- | :------------- |
| phpMyAdmin        | 127.0.0.1:8080 |
| symfony home page | 127.0.0.1:8741 |
| mailDev inbox     | 127.0.0.1:8081 |

**Here's the project directory structure:**

    snowtricks/
    ├── bin/
    ├── config/
    ├── docker/ (development environment)
    ├── migrations/
    ├── public/
    ├── src/
    ├── templates/
    ├── tests/
    ├── translations/
    ├── var/
    └── vendor/

* * *

## Development environment

`Docker` and `Docker-compose` are needed to use your dev environment.

Launch development environment.

```bash
cd docker

docker-compose up -d
```

It will install following components:

-   PHP 8.0
-   Apache 2.4.48
-   MySQL server 8.0.26
-   Composer

* * *

## Use doctrine

:warning: You have to use doctrine in the docker container bash.

Open a docker shell:

```bash
cd docker

docker exec -it www_docker_symfony bash
```

Then, you can use doctrine:

```bash
php bin/console doctrine:migrations:migrate
```
