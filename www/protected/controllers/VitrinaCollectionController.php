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
            $section = VitrinaSection::model()->findByPk($sectionId);
            if (!$section)
            {
                throw new CHttpException(404, 'Страница не найдена');
            }
            $parents = $section->getParents($section->id, 2);
            if (!$parents)
                $parents = array($section->id);
            if($parents)
                $this->setData('rootSectionUri', CHtml::normalizeUrl(array('/vitrinaCollection/section', 'sectionId'=>$parents[0])));
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
        if ($sectionId) {
            $this->setData('top_banner_name', 'section_'.$section->id);
            $this->setData('top_banner_description', 'Баннер в рубрике '.$section->name);
            $this->setPageTitle($section->name.' &mdash; одежда в Екатеринбурге, ассортимент магазинов с ценами и фото &mdash; '.Yii::app()->params['siteName']);
        }
        else {
            $this->setPageTitle('Одежда в Екатеринбурге, модные бренды и ассортимент магазинов с ценами и фото &mdash; Витринабург, Магазины в Екатеринбурге &mdash; '.Yii::app()->params['siteName']);
        }


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

        $index = 0;
        $i = 0;
        if ($photo && $collection->photos)
        {
            foreach ($collection->photos as $_photo)
            {
                if ($photo->id == $_photo->id)
                {
                    $index = $i;
                    break;
                }
                $i++;
            }
        }
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
        $section = new VitrinaSection;
        $parents = $section->getParents($selectedSections, 2);
        if($parents)
            $this->setData('rootSectionUri', CHtml::normalizeUrl(array('/vitrinaCollection/section', 'sectionId'=>$parents[0])));

        $model = new VitrinaShopCollection;
        $sectionIds = $model->relationIds('sections');

        $model = new VitrinaShopCollectionPhoto;
        $counters = $model->relationCountersByScope ('bySections', $sectionIds);

        if ($photo && !empty($photo->name))
            $pageTitle = $photo->name.' из коллекции &laquo;'.$collection->name.'&raquo; магазина &laquo;'.$collection->shopObj->name.'&raquo; в Екатеринбурге  &mdash; '.Yii::app()->params['siteName'];
        else
            $pageTitle = 'Фото коллекции &laquo;'.$collection->name.'&raquo; магазина &laquo;'.$collection->shopObj->name.'&raquo; &mdash; ассортимент модных магазинов Екатеринбурга с фото и ценами. &mdash; '.Yii::app()->params['siteName'];
        $this->setPageTitle($pageTitle);

        $this->render('collection', array(
            'collection' => $collection,
            'selectedSections' => $selectedSections,
            'counters' => $counters,
            'photo' => $photo,
            'index' => $index,
        ));
    }

}