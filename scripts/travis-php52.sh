#!/bin/bash
set -ev
wget http://getcomposer.org/composer.phar
alias composer='php composer.phar'
composer require "phpunit/phpunit:3.7.0" --no-update
