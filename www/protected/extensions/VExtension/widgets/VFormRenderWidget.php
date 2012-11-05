<?php
/*
 * Рендер формы типа Forms
 */
class FormsFormRenderWidget extends ExtendedWidget
{
    public $model; // FormsFormRender
    public $options;
    public $startPageIndex;

	public function run()
	{
        $this->model->startPageIndex = $this->startPageIndex;
        echo $this->model->render();
	}
}
?>