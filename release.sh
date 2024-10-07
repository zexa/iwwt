#/bin/sh
bin/console doctrine:migrations:migrate
php bin/console cache:clear
php bin/console cache:warmup --env=prod