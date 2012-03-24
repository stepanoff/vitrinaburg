<?php
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
return array(
	'title' => 'gpor-auth',
	'siteName' => 'gpor-auth',
	'appName' => 'gpor-auth',

    'yiiDebug' => true, // YII debug

    'domain' => 'auth.localhost',

    'cronLogsPath' => '', // обсолютный путь для хранения lock-файлов крона
	'phpPath' => '', // Path to php

    /* email */
    'adminEmail' => 'stenlex@gmail.com', // куда слать ошибки
    'senderEmail' => 'mailer@auth.localhost', // от кого слать ошибки

    'token_secret' => 'adsdasdweror84k', // используется для генерации одноразовых токенов
    'sessionPrefix' => 'auth_backend', // префикс для хранения данных сессии и куки
    'dbDriver' => 'redis', // какой БД пользоваться (redis, mysql)

    // для mysql
    'db_host' => '',
    'db_name' => '',
    'db_user' => '',
    'db_password' => '',


    'clients' => array (
        array (
            'login' => 'demo',
            'password' => 'demo',
        ),
    ),

    'redis_host' => '', // хост сервера редиса
    'redis_port' => '', // порт сервера редиса

    'twitter' => array(
        'key' => '',
        'secret' => '',
    ),

    'google_oauth' => array(
        'client_id' => '',
        'client_secret' => '',
    ),

    'facebook' => array(
        'client_id' => '',
        'client_secret' => '',
    ),

    'vkontakte' => array(
        'client_id' => '',
        'client_secret' => '',
    ),

    'mailru' => array(
        'client_id' => '',
        'client_secret' => '',
    ),

    'moikrug' => array(
        'client_id' => '',
        'client_secret' => '',
    ),

    'odnoklassniki' => array(
        'client_id' => '',
        'client_secret' => '',
        'client_public' => '...',
    ),


);
?>