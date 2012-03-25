<?php
$params = array();
$localConfigFile = dirname(__FILE__).DS.'../../localConfig/params.php';
$localDistConfigFile = dirname(__FILE__).DS.'../../localConfig/params-dist.php';
if (file_exists($localDistConfigFile))
	$localDistConfig = require($localDistConfigFile);
else
	die('local config-dist doesn`t exists at '.$localDistConfigFile."\n");
if (file_exists($localConfigFile))
	$localConfig = require($localConfigFile);
else
	die('local config doesn`t exists at '.$localConfigFile."\n");
$params = array_merge ($localDistConfig, $localConfig);
$emptyKeys = array();
foreach ($params as $k=>$v)
{
	if (is_string($v) && empty($v))
		$emptyKeys[] = $k;
}

/*
if (sizeof($emptyKeys))
{
	echo "Error: params\n".implode("\n", $emptyKeys)."\nrequired";
	die();
}
*/

return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>$params['appName'],
    'runtimePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'data',
    'language' => 'ru',
    'commandMap' => array(
//        'mailsend'                  => $extDir . DS . 'mailer' . DS . 'MailSendCommand.php',
    ),

	'preload'=>array('log'),

	// autoloading model, component and helper classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.extensions.*',
        'application.extensions.eoauth.*',
        'application.extensions.eoauth.lib.*',
        'application.extensions.lightopenid.*',
        'application.extensions.eauth.*',
        'application.extensions.eauth.services.*',
        'application.extensions.gporauth.*',
        'application.extensions.gporauth.custom_services.*',
        'application.extensions.gporauth.models.*',
		'application.helpers.*',
		'application.widgets.*',
),

	'params'=>$params,

	'components'=>array(
        'cron' => array(
			'class' => 'CronComponent',
			'logPath' => $params['cronLogsPath'],
		),

        'cache' => array(
			'class' => 'CFileCache',
			'cachePath' => ROOT_PATH. DS . 'protected' . DS . 'runtime' . DS . 'cache',
		),

        'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning, notice',
//					'levels'=>'error, warning',
				),
			),
		),
        'errorHandler' => array(
        	'class' => 'application.components.ExtendedErrorHandler'
        ),
        'loid' => array(
            'class' => 'ext.lightopenid.loid',
        ),
		'eauth' => require(dirname(__FILE__).'/eauth.php'),

		'clientScript'=>array(
			'class'=>'application.components.ExtendedClientScript',
			'combineFiles'=>false,
			'compressCss'=>false,
			'compressJs'=>false,
		),
        'urlManager'=>require(dirname(__FILE__).'/urlManager.php'),

        'cache' => array(
			'class' => 'CFileCache'
		),
        'db'=>array(
            'connectionString'=>'mysql:'.$params['db_host'].'=localhost;dbname='.$params['db_name'],
            'username'=>$params['db_user'],
            'password'=>$params['db_password'],
            'charset' => 'utf8',
            'autoConnect'=>true,
        ),

        'errorHandler' => array(
        	'class' => 'application.components.ExtendedErrorHandler'
        ),
    ),

    'modules'=>require(dirname(__FILE__).'/modules.php'),

);