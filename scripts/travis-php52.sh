#!/bin/bash
set -ev
wget http://getcomposer.org/composer.phar
alias composer='php composer.phar'
php composer.phar require "phpunit/phpunit:3.7.0" --no-update
