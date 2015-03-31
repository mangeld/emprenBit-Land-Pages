#!/bin/bash
export MYSQLPASSWD='toor'
mysql < dbTables.sql -uroot --password=$MYSQLPASSWD
php vendor/phpunit/phpunit/phpunit