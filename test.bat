@echo off
"vendor/bin/phpunit" --exclude-group=legacy --bootstrap vendor/autoload.php tests
