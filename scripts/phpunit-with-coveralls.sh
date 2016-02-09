#!/bin/bash
set -ev
mkdir -p build/logs
phpunit --coverage-clover=build/logs/clover.xml
composer require satooshi/php-coveralls
php vendor/bin/coveralls build/logs/clover.xml -v
