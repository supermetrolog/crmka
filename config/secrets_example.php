<?php

return [
    // Окружение: dev, prod, staging
    'yii_env' => 'dev',
    // Debug режим: true, false
    'yii_debug' => true,
    // FTP доступ для синхронизации текущего проекта на другой сервер
    'ftp_options_for_sync_this_project' => [
        'host' => '255.255.255.255',
        'username' => 'username',
        'password' => 'password',
    ],
    // FTP доступ для синхронизации объектового проекта на другой сервер
    'ftp_options_for_sync_objects_project' => [
        'host' => '255.255.255.255',
        'username' => 'username',
        'password' => 'password',
    ],
    // FTP доступ для синхронизации фронтенд проекта на другой сервер
    'ftp_options_for_sync_frontend_project' => [
        'host' => '255.255.255.255',
        'username' => 'username',
        'password' => 'password',
    ],
    // FTP доступ для бекапов на другой сервер
    'ftp_options_for_backups_load' => [
        'host' => '255.255.255.255',
        'username' => 'username',
        'password' => 'password',
    ],
    // В целом нигде не используется
    'adminEmail' => 'admin@mail.ru',
    // Email доступы, которые будет использоваться для отправки писем в случае, если у конкретного юзера не указаны доступы к его email
    'senderEmail' => 'company@mail.ru',
    'senderUsername' => 'username',
    'senderPassword' => 'password',
    // Телеграм бот и канал используются для логирования ошибок
    'tg_logger_bot' => [
        'token' => '###########:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
        'channel' => '@ChannelUsername'
    ],
    // Доступы к RabbitMQ серверу
    'rabbit' => [
        'user' => 'user',
        'password' => 'password'
    ],
    // Доступы для основной базы данных этого проекта
    'db' => [
        'host' => 'localhost',
        'dbname' => 'app',
        'username' => 'root',
        'password' => 'root'
    ],
    // Доступы для старой базы данных объектового проекта
    'db_old' => [
        'host' => 'localhost',
        'dbname' => 'app',
        'username' => 'root',
        'password' => 'root'
    ],
    // Доступы к основной базе данных на удаленном сервере. Используется для синхронизации базы данных между серверами
    'importer_db' => [
        'dbname' => 'app',
        'username' => 'root',
        'password' => 'root'
    ],
    // Доступы к объектовой базе данных на удаленном сервере. Используется для синхронизации базы данных между серверами
    'importer_db_old' => [
        'dbname' => 'app',
        'username' => 'root',
        'password' => 'root'
    ],
    // Доступы к удаленному серверу для выполнения на нем команд в частности используется при импорте баз данных
    'ssh' => [
        'reserve_server' => [
            'username' => 'username',
            'password' => 'password',
            'host' => '255.255.255.255',
        ]
    ],
];
