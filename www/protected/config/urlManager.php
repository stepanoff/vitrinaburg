<?php 
return array(
	'urlFormat'=>'path',
	'showScriptName'=>false,
    'urlSuffix' => '/',
	'rules'=>array(
        '/coll/cat<sectionId:([0-9]+)>'=>'vitrinaCollection/section/',
        '/coll/<collectionId:([0-9]+)>/<photoId:([0-9]+)>'=>'vitrinaCollection/show',
        '/coll/<collectionId:([0-9]+)>'=>'vitrinaCollection/show/',
        '/coll'=>'vitrinaCollection/index/',

        '/article'=>'vitrinaArticle/index',
        '/article/<id:([0-9]+)>'=>'vitrinaArticle/show',

        '/action'=>'vitrinaAction/index',
        '/action/<id:([0-9]+)>'=>'vitrinaAction/action',

        '/shop'=>'vitrinaShop/index',
        '/shop/<id:([0-9]+)>'=>'vitrinaShop/show',

        '/mystyle/showSet/<id:([0-9]+)>'=>'vitrinaWidget/show',
        '/mystyle/' => 'vitrinaWidget/create',

        '/<action:([a-zA-Z0-9_]+)>'=>'site/<action>',
	),
);
?>