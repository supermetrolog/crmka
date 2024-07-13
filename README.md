# CRM PROJECT

### Установка для разработки

1. склонировать репозиторий
2. запустить `docker compose up --build -d`
3. выполнить `composer install`
4. импортировать базы данных (сейчас на момент 09.07.2024 это crmka.sql и crmka_old.sql, как сеопировать без добавления pma я так и не понял)
5. скопировать в папке `config/` файл `secrets_example.php` в `secrets.php`
6. изменить в `secrets.php` 50-51 и 57-58 
7. выполнить `php yii migrate`
