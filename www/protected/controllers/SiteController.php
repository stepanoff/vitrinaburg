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
                            $returnUrl = isset($_GET['returnUrl']) ? $_GET['returnUrl'] : Yii::app()->user->returnUrl;
                            $this->redirect($returnUrl);
                            Yii::app()->end();
                            /*
                            $rUrl = isset($_GET['redirectUrl']) ? $_GET['redirectUrl'] : Yii::app()->user->returnUrl;
                            $rUrl .= (strstr('?', $rUrl) ? '&' : '?').'auth_token='.urlencode(Yii::app()->user->generateTemporaryToken());
                            $rUrl .= '&returnUrl='.urlencode($returnUrl);
                            $authIdentity->redirectUrl = $rUrl;
                            */
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
        // todo: убрать костыль
        Yii::app()->request->redirect('/article/?logout=1&returnUrl='.CHtml::encode($redirectUrl));
        Yii::app()->end();
    }

    public function actionForgetPass () {
        $this->setPageTitle('Вспомнить пароль &mdash; '.Yii::app()->params['siteName']);

        $form = new VitrinaForgetPasswordForm();
        $cForm = new VFormRender(array());
        $cForm->model = $form;
        $text = '';
		if ($cForm->submitted()) {
            if ($cForm->model->validate())
            {
                $attrs = $cForm->model->attributes;

                $user = VUser::model()->byEmail($attrs['email'])->find();

                if ($user) {
                    $message = array(
                        'subject' => 'Напоминание пароля на сайте '.Yii::app()->params['siteName'],
                        'from_email' => Yii::app()->params['senderEmail'],
                        'from_username' => '',
                        'to_email' => $user->email,
                        'to_username' => $user->username,
                        'html' => $this->renderPartial('application.views.mail.forgetPass', array(
                            'login' => $user->login,
                            'password' => $user->password,
                        ), true),
                    );
                    MailHelper::sendMail($message);

                    $cForm = false;
                    $text = '<p>Пароль выслан на указанный адрес.</p><p><a href="/">&larr; Вернуться на главную страницу</a></p>';
                }

            }
        }

        $this->render ('forgetPass', array(
            'form' => $cForm,
            'text' => $text,
        ));

    }

    public function actionRegisterShop () {
        if (Yii::app()->user->id)
        {
            $this->redirect('/');
        }

        $this->setPageTitle('Добавить свой магазин &mdash; '.Yii::app()->params['siteName']);

		$service = 'inner';
        $form = new RegisterShopForm();
        $cForm = new VFormRender(array());
        $cForm->model = $form;
		if ($cForm->submitted()) {
            if ($cForm->model->validate())
            {
                $attrs = $cForm->model->attributes;

                $pass = VStringHelper::generatePassword();

                // добавляем в контакты
                $values = array('contact'=>$attrs['contactName'].' ('.$attrs['phone'].')', 'pass'=>$pass, 'email'=>$attrs['email'], 'name'=>$attrs['shopName'], );
                $client = new VitrinaClient;
                $client->setAttributes($values);
                $client->save();

                // создаем пользователя
                $login = 's'.$client->id;
                $user = new VUser;
                $user->login = $login;
                $user->email = $attrs['email'];
                $user->username = $attrs['shopName'];
                $user->password = $pass;

                if ($user->save())
                {
                    $client->user = $user->id;
                    $client->login = $login;
                    $client->save();

                    $user->service = $service;
                    $user->serviceId = $user->id;
                    $user->save();

                    // todo: убить костыль
                    $oldUserValues = array(
                        'username' => $login,
                        'password' => $pass,
                        'open_pass' => $pass,
                        'email' => $user->email,
                        'id' => $user->id,
                    );
                    Yii::app()->db->commandBuilder->createInsertCommand('users', $oldUserValues)->execute();

                    $profileValues = array(
                        'user_id' => $user->id,
                        'name' => $attrs['shopName'],
                    );
                    Yii::app()->db->commandBuilder->createInsertCommand('user_profiles', $profileValues)->execute();

                    $rolesValues = array(
                        'user_id' => $user->id,
                        'role_id' => '2',
                    );
                    Yii::app()->db->commandBuilder->createInsertCommand('users_roles', $rolesValues)->execute();
                    // конец костыля

                    // даем клиенту магазин
                    $shop = new VitrinaShop;
                    $shop->name = $attrs['shopName'];
                    $shop->owner = $user->id;
                    $shop->status = VitrinaShop::STATUS_NEW;
                    if (!$shop->save()) {
                        print_r($shop->getErrors());
                        die();
                    }

                    // делаем рассылку
                    $message = array(
                        'subject' => 'Ваш магазин зарегистрирован на сайте '.Yii::app()->params['siteName'],
                        'from_email' => Yii::app()->params['senderEmail'],
                        'from_username' => '',
                        'to_email' => $attrs['email'],
                        'to_username' => $attrs['contactName'],
                        'html' => $this->renderPartial('application.views.mail.shopRegistration', array(
                            'contact' => $attrs['contactName'],
                            'shop' => $shop->name,
                            'login' => $user->login,
                            'password' => $user->password,
                        ), true)
                    );
                    MailHelper::sendMail($message);

                    $message = array(
                        'subject' => 'Новая регистрация на сайте '.Yii::app()->params['siteName'],
                        'from_email' => Yii::app()->params['senderEmail'],
                        'from_username' => '',
                        'to_email' => Yii::app()->params['adminEmail'],
                        'to_username' => $attrs['contactName'],
                        'html' => $this->renderPartial('application.views.mail.shopRegistrationAdmin', array(
                            'contact' => $attrs['contactName'],
                            'shop' => $shop->name,
                            'login' => $user->login,
                            'password' => $user->password,
                        ), true)
                    );
                    MailHelper::sendMail($message);


                    // авторизуем на сайте
                    $_POST['VAuthForm'] = array(
                        'login' => $login,
                        'password' => $pass,
                    );
                    $authIdentity = Yii::app()->vauth->getIdentity($service);
                    if ($authIdentity->authenticate()) {
                        $identity = new VAuthUserIdentity($authIdentity);
                        $rememberMe = 1;
                        if ($identity->authenticate()) {
                            Yii::app()->user->login($identity, $rememberMe);
                            Yii::app()->request->redirect('/myshop/');
                        }
                    }
                }

            }
        }

        $this->render ('registerShop', array(
            'form' => $cForm,
        ));

    }

}