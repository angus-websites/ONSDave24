
# ONSDave

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
