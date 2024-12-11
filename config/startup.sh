#!/bin/bash

service apache2 start
composer install
composer global require phpstan/phpstan
while : ; do sleep 1000; done
