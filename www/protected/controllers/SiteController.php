<?php
class SiteController extends Controller
{
    public $layout='column1';

    public function actionIndex()
    {
        $options = array(
        );

        $this->render('main', array('options' => $options));
    }

}