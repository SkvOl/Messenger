# Messenger
 Мессенджер
Этот проект является полноценным мессенджером по адрессу http://138.124.55.208.

Для удобства использования api были подняты следующие библиотеки
Swagger: http://138.124.55.208/api/documentation
Pulse: [http://138.124.55.208/pulse](http://138.124.55.208/pulse?period=7_days)

Был поднят сокетный сервер Reverb, часть взаимодействия с api происходит через сокеты.

На удалённом сервере было поднято 5 контейнеров docker:
php-fpm для Laravel 11,
nginx:alpine,
postgres разработческая,
postgres продакшн
