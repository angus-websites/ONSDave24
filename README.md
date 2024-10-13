
# ONSDave24

## Overview
DAVE is the Daily Attendance & Validation Engine, a Laravel application to allow employees to track their working hours.

## Run Locally (Laravel Sail)

Clone the project

```bash
git clone https://github.com/angus-websites/ONSDave24.git
```

Go to the project directory

```bash
cd ONSDave24
```

Setup Laravel Sail

**_NOTE:_**  Ensure you have Docker installed

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

Generate a .env file

```bash
cp .env.example .env
```
Run Laravel Sail (Development server)

**_TIP:_**  Add `./vendor/bin/sail` as an alias to `sail` on your machine to make this command easier to run

```bash
./vendor/bin/sail up
```

**Open a new Terminal tab in the same project root folder**

Generate an app encryption key

```bash
./vendor/bin/sail php artisan key:generate
```

Migrate the database

```bash
./vendor/bin/sail php artisan migrate
```

Seed the database

```bash
./vendor/bin/sail php artisan db:seed
```

Install NPM dependencies

```bash
./vendor/bin/sail npm i
```

Run Vite

```bash
./vendor/bin/sail npm run dev
```

Visit [Localhost](http://localhost/)


New version of ONSDave written in Laravel 11 

## Setting up PHPStorm php interpreter

1. Go to `Settings` > `PHP` > `Test Frameworks`
2. Click on `+` and select `PHPUnit by Remote Interpreter`
3. Click on `...` next to "CLI Interpreter"
4. Click on `+` and select `From Docker, Vagrant, VM, WSL, Remote...`
5. Select `Docker Compose`
6. Select `Service` as `laravel.test`
7. Click Apply and OK
8. In the `PHHPUnit Library` section ensure the "Path to script" is set to `/var/www/html/vendor/autoload.php`
9. Click Apply and OK

## Setting up PHPStorm Xdebug

1. Ensure the `SAIL_XDEBUG_MODE=develop,debug,coverage` is set in the `.env` file
