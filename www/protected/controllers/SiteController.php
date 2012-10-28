<?php
class SiteController extends Controller
{
    public $layout='column1';

    public function actionIndex()
    {
        $this->setPageTitle('Одежда в Екатеринбурге, модные бренды и ассортимент магазинов с ценами и фото &mdash; Витринабург, Магазины в Екатеринбурге &mdash; '.Yii::app()->params['siteName']);

        $photosInSections = array(); // фото за последние сутки по рубрикам

        $actions = VitrinaShopAction::model()->onSite()->orderDefault()->byLimit(4)->findAll();
        $todayActions = 0;

        $articles = VitrinaArticle::model()->onSite()->orderDefault()->byLimit(3)->findAll();

        $todayPhotos = VitrinaShopCollectionPhoto::model()->onSite()->byDate(time())->count();
        $photos = VitrinaShopCollectionPhoto::model()->onSite()->orderCreated()->byLimit(100)->findAll();
        shuffle($photos);

        $sets = VitrinaUserSet::model()->onSite()->orderDefault()->byLimit(10)->findAll();

        $sectionsClass = new VitrinaSection;
        $sections = $sectionsClass->getStructure(2);

        /*
         * Форумное общение
         */
        $m = Yii::app()->getModule('VForum');
        $discussionModel = new VForumDiscussion;
        $commentModel = new VForumDiscussionComment;
        $discussions = array();
        $comments = array();

        $sql = '
SELECT DISTINCT (d.id), d.title, lc.id as `cid`, lc.date
FROM (
SELECT MAX( c.date ) AS `date` , c.id AS `id` , c.forum_discussion_id AS forum_discussion_id
FROM `'.$commentModel->tableName().'` c
GROUP BY c.forum_discussion_id
) AS lc, `'.$discussionModel->tableName().'` d
WHERE lc.forum_discussion_id = d.id
ORDER BY lc.date DESC
LIMIT 5';

        $lastDiscussions = Yii::app()->db->commandBuilder->createSqlCommand($sql)->queryAll();

        $dIds = array();
        foreach ($lastDiscussions as $row)
        {
            $dIds[] = $row['id'];
            $lc = VForumDiscussionComment::model()->byObjectId('forum_discussion_id', $row['id'])->orderLast()->find();
            if ($lc && $lc->user)
                $comments[$row['id']] = $lc;
        }

        if ($dIds)
        {
            $discussionsModels = VForumDiscussion::model()->byIds($dIds)->findAll();
            $tmp = array ();
            foreach ($discussionsModels as $discussion)
            {
                if (!$discussion->user)
                    continue;
                $tmp[$discussion->id] = $discussion;
            }
            foreach ($dIds as $dId)
            {
                if (isset($tmp[$dId]))
                    $discussions[] = $tmp[$dId];
            }
        }

        // берем фото добавленные за полследние сутки

        $this->render('main', array(
            'actions' => $actions,
            'todayActions' => $todayActions,
            'sections' => $sections,
            'photos' => $photos,
            'todayPhotos' => $todayPhotos,
            'photosInSections' => $photosInSections,
            'actions' => $actions,
            'articles' => $articles,
            'sets' => $sets,
            'comments' => $comments,
            'discussions' => $discussions,
        ));
    }

    public function actionLogin () {
        $ajax = Yii::app()->request->isAjaxRequest;

		$service = Yii::app()->request->getQuery('service');
		if (isset($service)) {
            try {
                $authIdentity = Yii::app()->vauth->getIdentity($service);
                if ($authIdentity->authenticate()) {

                    if (isset($_GET['nopopup']))
                    {
                        $rUrl = isset($_GET['redirect_uri']) ? $_GET['redirect_uri'] : Yii::app()->user->returnUrl;
                        $rUrl .= (strstr('?', $rUrl) ? '&' : '?').'error='.urlencode($service);
                        $authIdentity->cancelUrl = $rUrl;
                    }
                    else
                        $authIdentity->cancelUrl = '#error:'.$service;

                    $identity = new VAuthUserIdentity($authIdentity);

                    // успешная авторизация
                    $rememberMe = true;
                    if ($identity->authenticate()) {
                        Yii::app()->user->login($identity, $rememberMe);
                        if (isset($_GET['nopopup']))
                        {
                            $returnUrl = isset($_GET['returnUrl']) ? $_GET['returnUrl'] : '';
                            $rUrl = isset($_GET['redirectUrl']) ? $_GET['redirectUrl'] : Yii::app()->user->returnUrl;
                            $rUrl .= (strstr('?', $rUrl) ? '&' : '?').'auth_token='.urlencode(Yii::app()->user->generateTemporaryToken());
                            $rUrl .= '&returnUrl='.urlencode($returnUrl);
                            $authIdentity->redirectUrl = $rUrl;
                        }
                        else
                            $authIdentity->redirectUrl = '#reload:1';

                        if ($ajax)
                        {
                            $data = array ('success' => true, 'redirect_url' => $authIdentity->getRedirectUrl());
                            echo CJSON::encode($data);
                            die();
                            Yii::app()->end();
                        }
                        else
                        {
                            // специальное перенаправления для корректного закрытия всплывающего окна
                            $authIdentity->redirect();
                        }
                    }
                    else {
                        // закрытие всплывающего окна и перенаправление на cancelUrl
                        $authIdentity->cancel();
                    }
                }
                die();
                // авторизация не удалась, перенаправляем на страницу входа
                if (!$ajax)
                    $this->redirect(array('site/login'));
                else
                    Yii::app()->vauth->cancel(false);
            }
            catch (EAuthException $e)
            {
                $error = $e->getMessage();
                $code = $e->getCode();
                if ($ajax)
                {
                    $data = array(
                        'error' => array (
                            'code' => $code,
                            'message' => $error,
                        )
                    );
                    echo CJSON::encode($data);
                    die();
                    Yii::app()->end();
                }
                else
                {
                    $data = array(
                        'error' => array (
                            'code' => $code,
                            'message' => $error,
                        )
                    );
                    if (isset($_GET['redirectUrl']))
                    {
                        $url = $_GET['redirectUrl'];
                        $url .= (strstr('?', $url) ? '&' : '?').'error='.urlencode($data['error']['message']);
                        $this->redirect($url);
                        die();
                        Yii::app()->end();
                    }
                }
            }
		}

        $data = array(
            'error' => array (
                'code' => 500,
                'message' => 'Ошибка аутентификации. Сервис не найден',
            )
        );
        echo CJSON::encode($data);
        die();
        Yii::app()->end();
    }


    public function actionRegister () {
        if (Yii::app()->user->id)
        {
            $this->redirect('/');
        }

		$service = 'inner';
        $form = new VRegisterForm;
		if (isset($_POST['VRegisterForm'])) {

            $form->setAttributes($_POST['VRegisterForm']);
            if ($form->validate())
            {
                $user = new VUser;
                $user->login = $form->login;
                $user->email = $form->login;
                $user->username = $form->username;
                $user->password = $form->password;
                if ($user->save())
                {
                    $user->service = $service;
                    $user->serviceId = $user->id;
                    $user->save();

                    $_POST['VAuthForm'] = $_POST['VRegisterForm'];
                    $authIdentity = Yii::app()->vauth->getIdentity($service);
                    if ($authIdentity->authenticate()) {
                        $identity = new VAuthUserIdentity($authIdentity);
                        $rememberMe = $form->rememberMe;
                        if ($identity->authenticate()) {
                            Yii::app()->user->login($identity, $rememberMe);
                            Yii::app()->request->redirect(Yii::app()->user->returnUrl);
                        }
                    }
                }
            }
        }

        $this->render ('register', array(
            'form' => $form,
        ));

    }

    public function actionLogout () {
        $redirectUrl = Yii::app()->user->returnUrl;
        Yii::app()->user->logout();
        if (isset($_GET['returnUrl']) && !empty($_GET['returnUrl']))
             $redirectUrl = $_GET['returnUrl'];
        Yii::app()->request->redirect($redirectUrl);
        Yii::app()->end();
    }

    public function actionUserMigration ()
    {
        $c = Yii::app()->user;

        $criteria = new CDbCriteria(array(
            'order' => '`id` ASC',
        ));
        $users = Yii::app()->db->commandBuilder->createFindCommand('users', $criteria)->queryAll();
        foreach ($users as $user)
        {
            $userExists = VUser::model()->findByPk($user['id']);
            if ($userExists)
                continue;

            $password = false;
            if (!empty($user['open_pass']))
                $password = $user['open_pass'];
            else
            {
                $criteria = new CDbCriteria(array(
                    'condition' => '`user` = "'.$user['id'].'"',
                ));
                $client = Yii::app()->db->commandBuilder->createFindCommand('obj_client', $criteria)->queryRow();
                if ($client)
                {
                    $password = $client['pass'];
                }
            }

            if (!$password)
                continue;

            $username = $user['username'];
            $name = $user['name'].(!empty($user['last_name']) ? ' '.$user['last_name'] : '');
            $gender = '';
            $avatar = '';
            $photo = '';
            $birthday = '';

            $criteria = new CDbCriteria(array(
                'condition' => '`user_id` = "'.$user['id'].'"',
            ));
            $profile = Yii::app()->db->commandBuilder->createFindCommand('user_profiles', $criteria)->queryRow();
            if ($profile)
            {
                $username = $profile['name'];
                $name = $profile['fullname'];
                $gender = $gender == 1 ? 'm' : ($gender == 2 ? 'f' : '');
                $avatar = $user['avatar'];
                $photo = $user['photo'];
                $birthday = ($user['day_birth'] && $user['month_birth']) ? $user['day_birth'].'.'.$user['month_birth'] . ($user['year_birth'] ? '.'.$user['year_birth'] : '') : '';
            }

            /*
            $roleId = 1;
            $criteria = new CDbCriteria(array(
                'condition' => '`user_id` = "'.$user['id'].'"',
            ));
            $role = Yii::app()->db->commandBuilder->createFindCommand('user_roles', $criteria)->queryRow();
            if ($role)
            {
                $roleId = $role['role_id'];
            }
            */

            $newUser = new VUser;
            $newUser->id = $user['id'];
            $newUser->name = $name;
            $newUser->service = '';
            $newUser->serviceId = $user['id'];
            $newUser->avatar = $avatar;
            $newUser->email = $user['email'];
            $newUser->username = $username;
            $newUser->gender = $gender;
            $newUser->url = '';
            $newUser->photo = $photo;
            $newUser->updated = date ('Y-m-d 00:00:00');
            $newUser->login = $user['username'];
            $newUser->password = $password;
            $newUser->birthday = $birthday;

            $newUser->save();
        }

        echo 'migration completed';
        die();

    }

}