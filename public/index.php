<?php

//Указание пути к директории приложения
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH',
              realpath(dirname(__FILE__) . '/../application'));
//Определение текущего режима работы приложения. При желании устанавливается в apache/conf/httpd.conf строкой "SetEnv APPLICATION_ENV development"

define('APPLICATION_ENV', 'development');
if (!ini_get('display_errors')) {
    ini_set('display_errors', '1');
}
ini_set('error_reporting', E_ALL);

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
              (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

//Добавляем директорию библиотеки классов в список путей поиска
set_include_path(
    realpath('../library')
    . PATH_SEPARATOR
    . get_include_path()
);

require_once 'Zend/Application.php';

//Создание объекта приложения
$application = new Zend_Application(APPLICATION_ENV);
//Конфигурирование автозагрузчика файлов
$application->getAutoloader()
            ->registerNamespace('Lis_')
            ->registerNamespace('RealEstate_');
if ('development' == $application->getEnvironment()) {
    $application->getAutoloader()->registerNamespace('FirePHP_');
}
//Загрузка ресурсов и запуск цикла диспетчиризации
$application->setBootstrap(APPLICATION_PATH . '/Bootstrap.php', 'Bootstrap')
            ->bootstrap()
            ->run();