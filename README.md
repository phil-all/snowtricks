# Snowtricks

Collaborative web site, about snowboard tricks

* * *

## Table of contents

-   [Installation](#installation)
-   [Development environment](#development-environment)

* * *

## Installation

Install your project in a new folder, for example `snowtricks`.

```bash
mkdir snowtricks

cd snowtricks

git clone ...

composer install

npm run build
```

`snowtricks` folder will be your symfony working directory.

You will be able to access following pages, after launching your [development environment](#development-environment):

| Page              | Address        |
| :---------------- | :------------- |
| phpMyAdmin        | 127.0.0.1:8080 |
| symfony home page | 127.0.0.1:8741 |
| mailDev inbox     | 127.0.0.1:8081 |

## Make upload folder writable

Change the directory rights

```bash
chown -R www-data:www-data public/uploads

chmod -R 770 public/uploads
```

* * *

## Development environment

### Launch

`Docker` and `Docker-compose` are needed to use your dev environment.

Launch development environment in docker folder.

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

### Use

:warning: You have to use the docker container bash.

Open a shell in root project and launch the docker bash trough the composer.json script `dev-bash`.

```bash
composer dev-bash
```

### Database

After docker bash lauching, you can create (or recreate) database, update schema and load fixtures trough the composer.json script `set-db`.

```bash
composer set-db
```
