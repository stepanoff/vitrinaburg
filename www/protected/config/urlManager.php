<?php 
return array(
	'urlFormat'=>'path',
	'showScriptName'=>false,
    'urlSuffix' => '/',
	'rules'=>array(
        '/test' => 'site/userMigration',

        '/login' => 'site/login',
        '/register' => 'site/register',
        '/logout' => 'site/logout',

        '/coll/cat<sectionId:([0-9]+)>'=>'vitrinaCollection/section/',
        '/coll/<collectionId:([0-9]+)>/<photoId:([0-9]+)>'=>'vitrinaCollection/show',
        '/coll/<collectionId:([0-9]+)>'=>'vitrinaCollection/show/',
        '/coll'=>'vitrinaCollection/index/',

        '/article'=>'vitrinaArticle/index',
        '/article/<id:([0-9]+)>'=>'vitrinaArticle/show',

        '/action'=>'vitrinaAction/index',
        '/action/<id:([0-9]+)>'=>'vitrinaAction/action',

        '/shop/mall<mallId:([0-9]+)>'=>'vitrinaShop/index',
        '/shop/<id:([0-9]+)>'=>'vitrinaShop/show',
        '/shop'=>'vitrinaShop/index',

        '/mall'=>'vitrinaMall/index',
        '/mall/<id:([0-9]+)>'=>'vitrinaMall/show',

        '/mystyle/showSet/<id:([0-9]+)>'=>'vitrinaWidget/show',
        '/mystyle/' => 'vitrinaWidget/create',

        '/forum' => 'VForum/VForum/index',
        '/forum/addDiscussion' => 'VForum/VForum/addDiscussion',
        '/forum/discussion/<id:([0-9]+)>' => 'VForum/VForum/discussion',
        '/forum/removeComment/<id:([0-9]+)>' => 'VForum/VForum/removeComment',
        '/user/profile/<id:([0-9]+)>' => 'vitrinaForum/user',

        '/<staticPage:([a-zA-Z0-9_]+)>'=>'staticPage/show',
	),
);
?>