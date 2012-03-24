<?php 
return array(
	'urlFormat'=>'path',
	'showScriptName'=>false,
    'urlSuffix' => '/',
	'rules'=>array(
        '/<action:([a-zA-Z0-9_]+)>'=>'site/<action>',
	),
);
?>