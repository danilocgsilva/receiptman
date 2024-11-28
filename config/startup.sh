#!/bin/bash

service apache2 start
composer install
while : ; do sleep 1000; done
