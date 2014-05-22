<?php
/*
 * Рендер формы типа VFormRender
 */
class VFormRenderWidget extends ExtendedWidget
{
    public $model; // FormsFormRender
    public $options;
    public $startPageIndex;

	public function run()
	{
        $this->registerAssets();
        $this->model->startPageIndex = $this->startPageIndex;
        echo $this->model->render();
	}
}
?>