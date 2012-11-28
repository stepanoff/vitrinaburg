<?php 
date_default_timezone_set('Asia/Yekaterinburg');
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('YII_DEBUG') or define('YII_DEBUG',true);
error_reporting(E_ALL | E_STRICT);

mb_internal_encoding("UTF-8");

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__FILE__). DS . '..');
define('BASE_PATH', dirname(__FILE__). DS . '..' . DS . '..');
define('FILES_PATH', dirname(__FILE__). DS . '..' . DS . 'files');
define('LIB_PATH', realpath(dirname(__FILE__). DS . '..' . DS . '..' . DS . 'lib'));

// подключаем файл инициализации Yii
require_once(LIB_PATH .'/yii/framework/yii.php');

$configFile=dirname(__FILE__).'/config/console.php';

require(dirname(__FILE__) . '/components/ExtendedConsoleApplication.php');
$yiiConsoleApp = Yii::createApplication('ExtendedConsoleApplication', $configFile);
//Yii::createConsoleApplication($configFile)->run();
//Yii::import('application.extensions.croncommand.*');
?>