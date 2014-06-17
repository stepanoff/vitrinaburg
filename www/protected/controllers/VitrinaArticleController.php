<?php
class VitrinaArticleController extends Controller
{
    const ON_PAGE = 24;

    public $layout='v2_article';

    public function actionIndex()
    {
        $model = new VitrinaArticle;

        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = self::ON_PAGE;
        $offset = $page ? $page - 1 : 0;

        $criteria = new CDbCriteria(array(
            'limit' => $limit,
            'offset' => $offset * $limit,
        ));
        $items = $model->onSite()->orderDefault()->findAll($criteria);

        $countModel = new VitrinaArticle;
        $itemsTotal = $countModel->onSite()->count();
        $pages = new CPagination($itemsTotal);
        $pages->setCurrentPage( ($page-1) );
        $pages->pageSize = self::ON_PAGE;

        $this->render('list', array(
            'items' => $items,
            'pages' => $pages,
        ));
    }

    public function actionShow()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : false;
        if (!$id)
            throw new CHttpException(404);

        $item = VitrinaArticle::model()->findByPk($id);
        if (!$item)
            throw new CHttpException(404);

        $this->setPageTitle($item->title . ' &mdash; ' . Yii::app()->params['siteName']);

        $shareOptions = array(
            'link' => 'http://' . Yii::app()->params['domain'] . CHtml::normalizeUrl(array('/vitrinaArticle/show', 'id'=>$item->id)),
            'linkText' => $item->title,
            'annotation' => $item->announce,
            'imageUrl' => VHtml::thumbSrc($item->img, array(250, false), VHtml::SCALE_WIDTH),
        );
        $this->setData('shareOptions', $shareOptions);

        $this->render('show', array(
            'item' => $item,
        ));
    }

}