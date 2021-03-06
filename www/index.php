<?php
date_default_timezone_set('Asia/Yekaterinburg');

// change the following paths if necessary
$yii=dirname(__FILE__).'/../lib/yii/framework/yii.php';

$localConfig = @include(dirname(__FILE__) . '/localConfig/params.php');
$yiiDebug = (!empty($localConfig) && isset($localConfig['yiiDebug'])) ? $localConfig['yiiDebug'] : false;

$config=dirname(__FILE__).'/protected/config/main.php';

$ezComponentsBase = dirname(__FILE__).'/../lib/ezcomponents-2009.2.1/Base/src/base.php';
require_once($ezComponentsBase);
spl_autoload_register(array('ezcBase', 'autoload'));

define('ROOT_PATH', dirname(__FILE__));
define('BASE_PATH', dirname(__FILE__). DS . '..');
define('FILES_PATH', dirname(__FILE__). DS . 'files');
define('LIB_PATH', dirname(__FILE__). DS . '..' . DS . 'lib');

defined('YII_DEBUG') or define('YII_DEBUG', $yiiDebug);
defined('YII_DEBUG_LOG') or define('YII_DEBUG_LOG', $yiiDebug);

require_once($yii);
require(dirname(__FILE__) . '/protected/components/ExtendedWebApplication.php');
$app = Yii::createApplication('ExtendedWebApplication', $config);
Yii::app()->VExtension;
Yii::app()->user;
$app->run();
