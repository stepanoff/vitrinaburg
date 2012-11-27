<?php
class VitrinaAdminShopController extends VAdminController
{
    public $model = 'VitrinaShop';

    public function getListColumns() {
        return array(
            'name',
            array(
                'class'=>'VAdminShowHideWidget',
                'itemVisible' => '$data->isVisible ()'
            ),
            array(
                'class'=>'VAdminButtonWidget',
            ),
        );
    }

}