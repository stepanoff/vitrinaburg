<?php
class SiteController extends Controller
{
    public $layout='column1';

    public function actionIndex()
    {
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