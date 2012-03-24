<?php 
return array(
	'urlFormat'=>'path',
	'showScriptName'=>false,
    'urlSuffix' => '/',
	'rules'=>array(
        '/auth/<action:([a-zA-Z0-9_]+)>'=>'auth/<action>',
        '/<action:([a-zA-Z0-9_]+)>'=>'site/<action>',
	),
);
?>