<?php
class SiteController extends Controller
{
    public $layout='column1';

    public function actionIndex()
    {
        $this->setPageTitle('Одежда в Екатеринбурге, модные бренды и ассортимент магазинов с ценами и фото &mdash; Витринабург, Магазины в Екатеринбурге &mdash; '.Yii::app()->params['siteName']);

        $photosInSections = array(); // фото за последние сутки по рубрикам
        $answers = array(); // послдение ответы на форуме

        $actions = VitrinaShopAction::model()->onSite()->orderDefault()->byLimit(4)->findAll();
        $todayActions = 0;

        $articles = VitrinaArticle::model()->onSite()->orderDefault()->byLimit(3)->findAll();

        $todayPhotos = VitrinaShopCollectionPhoto::model()->onSite()->byDate(time())->findAll();
        $photos = VitrinaShopCollectionPhoto::model()->onSite()->orderCreated()->byLimit(100)->findAll();
        shuffle($photos);

        $sets = VitrinaUserSet::model()->onSite()->orderDefault()->byLimit(10)->findAll();

        $sectionsClass = new VitrinaSection;
        $sections = $sectionsClass->getStructure(2);

        $criteria = new CDbCriteria(array(
            'order' => '`date` DESC',
            'limit' => '5',
        ));
        $answers = Yii::app()->db->commandBuilder->createFindCommand('forum_comments', $criteria)->queryAll();
        foreach ($answers as $k => $answer)
        {
            $answer['discussion'] = false;

            $criteria = new CDbCriteria(array());
            $criteria->addCondition('id = :id');
            $criteria->params = array(
                ':id' => $answer['forum_discussion_id']
            );
            $discussion = Yii::app()->db->commandBuilder->createFindCommand('forum_discussions', $criteria)->queryRow();
            if ($discussion)
            {
                $answer['discussion'] = $discussion;
                $answer['user'] = false;
                $criteria = new CDbCriteria(array());
                $criteria->addCondition('id = :id');
                $criteria->params = array(
                    ':id' =>$answer['user_id']
                );
                $user = Yii::app()->db->commandBuilder->createFindCommand('users', $criteria)->queryRow();
                if ($user)
                    $answer['user'] = $user;
            }
            $answers[$k] = $answer;
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
            'answers' => $answers,
        ));
    }

}