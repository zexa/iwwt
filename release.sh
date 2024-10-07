#/bin/sh
php bin/console cache:clear
php bin/console cache:warmup --env=prod