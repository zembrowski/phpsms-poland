#!/bin/bash
set -ev
mkdir -p build/logs
phpunit --coverage-clover=build/logs/clover.xml
php vendor/bin/coveralls -v
