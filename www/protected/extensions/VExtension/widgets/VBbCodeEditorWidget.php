<?php
class VFormBuilderWidget extends CWidget
{
	public $form;
	public $model;
    public $elements;
	public $formInputLayout = '<div class="form-row">{label}{input}{hint}<div class="form-row__error">{error}</div></div>';
	public $formErrorInputLayout = '<div class="form-row error">{label}{input}{hint}<div class="form-row__error">{error}</div></div>';
    public $renderButtons = true;
    public $defaultClasses = array(
//		'text'=>'grid-span-7',
//		'password'=>'grid-span-7',
//		'textarea'=>'grid-span-9',
//		'file'=>'grid-span-7',
//		'listbox'=>'grid-span-7',
//		'dropdownlist'=>'grid-span-7',
	);
    public $defaultOptions = array(
		'action'=>'',
		'method'=>'post',
        'htmlOptions' => array('class' => 'v-form'),
	);
	public $renderSafeAttributes = false;

	public function run()
	{
        $cs = Yii::app()->clientScript;
        $url = Yii::app()->VExtension->getAssetsUrl();
        $cs->registerCssFile($url.'/css/vform.css');

		$form = $this->form;
        if ($form === null) {
            $form = $this->_buildForm($this->model, $this->elements);
        }
        if (!$form)
            return;

        $form->activeForm = array_merge($this->defaultOptions, $form->activeForm );
		echo $form->renderBegin();
        /*
		foreach ($form->models as $m)
		{
			echo CHtml::errorSummary($m, 'Исправьте ошибки:');
		}
        */

		$this->renderFormElements($form);

        if ($this->renderButtons)
    		echo '<div class="buttons">'.$form->renderButtons().'</div>';
		echo $form->renderEnd();
	}

    protected function _buildForm ($model, $elements)
    {
        if (!is_array($elements))
            return false;

        if ($this->renderButtons)
        {
            if (!isset($elements['buttons']))
                $elements['buttons'] = array();
        }
        else
        {
            unset($elements['buttons']);
        }

        if (!isset($elements['enctype']))
            $elements['enctype'] = 'multipart/form-data';

        $form = new CForm ($elements);
        $form->model = $model;

        return $form;

    }


	public function renderFormElements ($form)
	{
		$errors = $this->model->getErrors();
		if($form->title){
			echo '<h3>'.$form->title.'</h3><hr/>';
		}
		foreach ($form->getElements() as $k=>$element)
		{
			if (get_class($element)=='CFormStringElement')
			{
				$element->layout = $this->formInputLayout;
				echo $element->render();
			}
			elseif (get_class($element)!='CFormInputElement')
			{
				if ($element->model)
				{
					$this->renderFormElements($element);
				}
			}
			else
			{
				$error = false;
				if(!empty($errors[$element->name])){
					$error = $errors[$element->name][0];
//					echo '<div class="alert">';
				}
				if ($this->renderSafeAttributes && !$this->model->isAttributeSafe($k))
					continue;

                if(isset($this->defaultClasses[$element->type])){ // if we have default value
                    if(!array_key_exists('class',$element->attributes)) { // but we have no attribute class defined
                        $element->attributes['class'] = $this->defaultClasses[$element->type]; // default will be set
                    } elseif (is_array($element->attributes['class'])){ // but if array defined
                        foreach ($element->attributes['class'] as $key => $param){ // every key
                            if(!strpos($param,'grid') && isset($this->defaultClasses[$element->type][$key])) //will be checked
                                $element->attributes['class'][$key] = $this->defaultClasses[$element->type][$key]; // and applied if exists not yet
                        }
                    } elseif (!@strpos($element->attributes['class'],'grid')) { // if we have no str 'grid' in class str
                    }
                }
                if ($error)
                    $element->layout = $this->formErrorInputLayout;
                else
                    $element->layout = $this->formInputLayout;
				echo $element->render();
				if(!empty($element->attributes['description'])) {
					echo '<div class="hint value row">' . $element->attributes['description'] . '</div>';
				}

				//if ($error) echo "</div>";

			}
		}
	}
}
?>