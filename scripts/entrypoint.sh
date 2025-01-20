cd /var/www/messenger && php artisan --silent queue:work
cd /var/www/messenger && php artisan --silent reverb:start --port=9001 --host=0.0.0.0