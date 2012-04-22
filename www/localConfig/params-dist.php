<?php
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
return array(
	'title' => '',
	'siteName' => '',
	'appName' => '',

    'yiiDebug' => true, // YII debug

    'domain' => '',

    'cronLogsPath' => '', // обсолютный путь для хранения lock-файлов крона
	'phpPath' => '', // Path to php
    'filesPath' => '', // путь к хранилищу файлов
    'filesUrl' => '', // урл для доступа к файлам через браузер
    'staticUrl' => '', // урл до статики

    'yandexMapsKey' => 'ANoq1UwBAAAAV_mwUwIAndBPjX2mFzYxnnfrwPBRnnA_kPYAAAAAAAAAAABSxYjGqzFJWpnfiX2RcIqn_kcg1w==',

    /* email */
    'adminEmail' => 'stenlex@gmail.com', // куда слать ошибки
    'senderEmail' => 'mailer@auth.localhost', // от кого слать ошибки

    // для mysql
    'db_host' => '',
    'db_name' => '',
    'db_user' => '',
    'db_password' => '',


    'twitter' => array(
        'key' => '',
        'secret' => '',
    ),

    'facebook' => array(
        'client_id' => '',
        'client_secret' => '',
    ),

    'vkontakte' => array(
        'client_id' => '',
        'client_secret' => '',
    ),

);
?>