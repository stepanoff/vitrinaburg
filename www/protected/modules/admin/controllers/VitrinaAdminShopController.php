<?php
class VitrinaAdminShopController extends VAdminController
{
    public $model = 'VitrinaShop';

    public $route = '/admin/VitrinaAdminShop/index';

    public function layoutsFilters() {
        $statuses = VitrinaShop::statusTypes();
        $statuses[""] = 'Выбрать';
        return array(
            'name' => array(
                'type' => 'text',
                'label' => 'Название',
            ),
            'status' => array(
                'type' => 'dropdownlist',
                'items' => $statuses,
                'label' => 'Статус',
            ),
        );
    }

    public function appendLayoutFilters($model, $cFilterForm) {
        if ($cFilterForm->model->status != "") {
            $model->byStatus($cFilterForm->model->status);
        }
        if ($cFilterForm->model->name != "") {
            $model->getDbCriteria()->addSearchCondition('name', $cFilterForm->model->name);
        }
        return $model;
    }

    public function getListColumns() {
        $statuses = VitrinaShop::statusTypes();
        return array(
            'name',
            array(
                'class'=>'VAdminSelectWidget',
                'data' => $statuses,
                'value' => '$data->status',
                'label' => false,
                'inputName' => 'status',
                'url' => 'Yii::app()->controller->createUrl("setStatus",array("id"=>$data->primaryKey))'
            ),
            array(
                'class'=>'VAdminShowHideWidget',
                'itemVisible' => '$data->isVisible ()'
            ),
            array(
                'class'=>'VAdminButtonWidget',
            ),
        );
    }

    public function getFormElements() {
        $brands = VitrinaBrand::model()->orderDefault()->findAll();
        $items = CHtml::listData($brands, 'id', 'name');
        $res = array(
            'name' => array(
                'type' => 'text',
            ),
            'site' => array(
                'type' => 'text',
            ),
            'brandsIds' => array(
                'type' => 'VHtmlMultiSelectWidget',
                'data' => $items,
            ),
            'text' => array(
                'type' => 'VHtmlCkEditorWidget',
            ),
        );
        return $res;
    }

}