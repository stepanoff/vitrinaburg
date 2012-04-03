<?php
class VitrinaCollectionController extends Controller
{
    const ON_PAGE = 20;

    public $layout='column1';

    public function actionSection()
    {
        $sectionId = isset($_GET['sectionId']) ? (int) $_GET['sectionId'] : false;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        $model = new VitrinaShopCollectionPhoto;
        $countModel = new VitrinaShopCollectionPhoto;

        if ($sectionId)
        {
            $model->bySections($sectionId);
            $countModel->bySections($sectionId);
        }

        $itemsTotal = $countModel->count();
        $pages = new CPagination($itemsTotal);
        $pages->setCurrentPage( ($page-1) );
        $pages->pageSize = self::ON_PAGE;

        $model->getDbCriteria()->mergeWith(array(
            'limit' => self::ON_PAGE,
            'offset' => ($page-1)*self::ON_PAGE,
        ));

        $items = $model->orderRand()->findAll();

        $model = new VitrinaShopCollection;
        $sectionIds = $model->relationIds('sections');

        $model = new VitrinaShopCollectionPhoto;
        $counters = $model->relationCountersByScope ('bySections', $sectionIds);

        /*
		if (Yii::app()->request->isAjaxRequest)
		{
			$listPart = $this->renderPartial($this->__templates['list'], array('list' => $list, 'pages' => $pages), true);

			$this->setAjaxData('list', $listPart);
			//$this->setAjaxData ('eval', 'AjaxCallback();');
		}
		else
		{
			$this->render('banki.views.banki.admin.sub.list', array('list' => $list, 'pages' => $pages, 'template' => $this->__templates['list'], 'bank'=>$bank));
		}
        */


        $this->render('section', array(
            'items' => $items,
            'sectionId' => $sectionId,
            'counters' => $counters,
            'pages' => $pages,
        ));
    }

    public function actionShow()
    {
        $id = isset($_GET['collectionId']) ? (int) $_GET['collectionId'] : false;
        $photoId = isset($_GET['photoId']) ? (int) $_GET['photoId'] : false;
        if (!$id)
            throw new CHttpException(404);

        $collection = VitrinaShopCollection::model()->findByPk($id);
        if (!$collection)
            throw new CHttpException(404);

        $photo = false;
        if ($photoId)
            $photo = VitrinaShopCollectionPhoto::model()->findByPk($photoId);
        if (!$photo || $photo->shopcollect != $id)
            $photo = false;

        /*
		if (Yii::app()->request->isAjaxRequest)
		{
			$listPart = $this->renderPartial($this->__templates['list'], array('list' => $list, 'pages' => $pages), true);

			$this->setAjaxData('list', $listPart);
			//$this->setAjaxData ('eval', 'AjaxCallback();');
		}
		else
		{
			$this->render('banki.views.banki.admin.sub.list', array('list' => $list, 'pages' => $pages, 'template' => $this->__templates['list'], 'bank'=>$bank));
		}
        */

        $selectedSections = $collection->getRelatedIds('sections');

        $model = new VitrinaShopCollection;
        $sectionIds = $model->relationIds('sections');

        $model = new VitrinaShopCollectionPhoto;
        $counters = $model->relationCountersByScope ('bySections', $sectionIds);

        $this->render('collection', array(
            'collection' => $collection,
            'selectedSections' => $selectedSections,
            'counters' => $counters,
            'photo' => $photo,
        ));
    }

}