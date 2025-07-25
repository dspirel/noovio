web: mkdir -p /tmp/sessions && php bin/console cache:clear && php bin/console asset-map:compile && heroku-php-apache2 public/
worker: php bin/console app:send-scheduled-webhooks --loop
