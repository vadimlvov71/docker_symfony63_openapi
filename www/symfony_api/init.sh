#!/bin/bash

php bin/console doctrine:database:create 
yes | bin/console doctrine:migrations:migrate 
yes | php bin/console make:fixture
yes | php bin/console doctrine:fixture:load