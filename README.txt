На windows с docker.
В папке проекта выполнить команду: docker-compose -f docker-compose.all.yml up -d
Контейнер symapi_php должен быть запущен, иначе symapi_nginx не будет работать.
И терминале контейнера symapi_php выполнить команду: composer install и php bin/console doctrine:migrations:migrate

На Linux системах.
В папке проекта app выполняем: composer install и php bin/console doctrine:migrations:migrate

Сколько писал задачу: 2 часа писал docker контейнер и прописывал версии для php7.4, потом 1.5 часа на остальной код.