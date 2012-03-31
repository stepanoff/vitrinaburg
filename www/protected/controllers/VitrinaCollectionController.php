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
            // todo:
            $selectedSecions = array();
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

}