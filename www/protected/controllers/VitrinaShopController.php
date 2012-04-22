<?php
class VitrinaShopController extends Controller
{
    public $layout='column1';

    public function actionIndex()
    {
        $mallId = isset($_GET['mallId']) ? (int) $_GET['mallId'] : false;

        $model = new VitrinaShop;

        if ($mallId)
        {
            $mall = VitrinaMall::model()->findByPk($mallId);
            if (!$mall)
            {
                throw new CHttpException(404, 'Страница не найдена');
            }
            $model->byMall($mallId);
        }
        $items = $model->onSite()->orderName()->findAll();

        $mallsStructure = $this->getMallsStructure();
        $counters = $this->getMallsCounters(array_keys($mallsStructure[0]['children']));

        $this->render('list', array(
            'items' => $items,
            'mallId' => $mallId,
            'counters' => $counters,
            'mallsStructure' => $mallsStructure,
            'selectedMalls' => $mallId ? array(0, $mallId) : array(0),
        ));
    }

    public function actionShow()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : false;
        if (!$id)
            throw new CHttpException(404);

        $shop = VitrinaShop::model()->findByPk($id);
        if (!$shop)
            throw new CHttpException(404);

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

        $selectedSections = array();

        $mallsStructure = $this->getMallsStructure();
        $counters = $this->getMallsCounters(array_keys($mallsStructure[0]['children']));

        $this->render('shop', array(
            'item' => $shop,
            'counters' => $counters,
            'mallsStructure' => $mallsStructure,
            'selectedMalls' => array(0),
        ));
    }

    protected function getMallsStructure ()
    {
        $malls = array();
        $mallModel = new VitrinaMall;
        $criteria = new CDbCriteria();
        $criteria->order = '`name` ASC';
        $mallRows = Yii::app()->db->commandBuilder->createFindCommand($mallModel->tableName(), $criteria)->queryAll();
        foreach ($mallRows as $row)
        {
            $malls[$row['id']] = array (
                'id' => $row['id'],
                'name' => $row['name'],
                'children' => array(),
            );
        }
        $mallsStructure = array (
            array(
                'id' => 0,
                'name' => 'Все магазины',
                'children' => $malls,
            ),
        );

        return $mallsStructure;
    }

    protected function getMallsCounters ($mallIds)
    {
        $model = new VitrinaShop;
        $counters = $model->relationCountersByScope ('byMall', $mallIds, array('onSite'=>array()));
        return $counters;
    }

}