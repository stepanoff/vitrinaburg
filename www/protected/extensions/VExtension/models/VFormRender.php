<?php
/**
 * Модель формопостроителя
 * @author stepanoff
 * @version 1.0
 * todo: вынести верстку в шаблоны. Класс ошики изначально не рендерится
 */
class VFormRender extends CForm
{
    public $formInputLayout = '<div class="form-row">{label}{input}{hint}<div class="form-row__error">{error}</div></div>';
    public $formErrorLayout = '{error}';
    public $stepJs = false;
    public $defaultClasses = array(
        'text'=>'grid-span-7',
        'password'=>'grid-span-7',
        'textarea'=>'grid-span-9',
        'file'=>'grid-span-7',
        'listbox'=>'grid-span-7',
        'dropdownlist'=>'grid-span-7',
    );
    public $renderSafeAttributes = false;
    public $startPageIndex = false;

    protected $output = '';

    public function init() {
    }

    protected function getUniqueId()
    {
        return 'vform_'.get_class($this->model);
    }

    public function render()
    {
        if ($this->startPageIndex !== false) {
            $i = 0;
            foreach ($this->model->getStepsStructure() as $page_id => $pageElements) {
                if ($i == $this->startPageIndex) {
                    $this->model->activePageId = $page_id;
                    break;
                }
                $i++;
            }
        }
        $model = $this->model;
        if (method_exists($model, 'getFormRenderData'))
            $this->configure($this->model->getFormRenderData());

        $oldReq = CHtml::$afterRequiredLabel;
        CHtml::$afterRequiredLabel = '&nbsp;<span class="b-form__label__star">*</span>';
        $this->output = '';
        $this->output .=  $this->renderBegin();

        $this->output .= $this->renderFormElements();

        $this->output .=  $this->renderButtons();
        $this->output .=  $this->renderEnd();
        if ($this->stepJs)
            $this->output .= $this->renderStepsJs();
        CHtml::$afterRequiredLabel = $oldReq;
        return $this->output;
    }


    public function renderStepsJs () {
        $output = '';
        /*
        $output = '<script type="text/javascript">';
        $output .= "
app.module.register( 'jsForm".$this->getUniqueId()."', js_steps_form, {
        'containerClass' : 'js-form".$this->getUniqueId()."',
        'stepClass' : 'forms__step',
        'activeStepClass' : 'forms__step_state_active',

        'buttonClass' : 'forms__button',
        'activeButtonClass' : 'forms__button_state_active',
        'ajax' : true

    });
        ";
        $output .= '</script>';
        */
        return $output;
    }

    public function renderFormElements ()
    {
        $output = '';
        $errors = $this->model->getErrors();
        if($this->title)
            $output .= '<h3>'.$this->title.'</h3><hr/>';

        foreach ($this->getElements() as $k=>$element)
        {
            if (get_class($element)=='CFormStringElement')
            {
                $element->layout = $this->formInputLayout;
                $output .= $element->render();
            }
            elseif (get_class($element)!='CFormInputElement')
            {
                if ($element->model)
                    $this->renderFormElements($element);
            }
            else
            {
                $error = false;
                if(!empty($errors[$element->name]))
                {
                    $error = $errors[$element->name][0];
                }
                if ($this->renderSafeAttributes && !$this->form->model->isAttributeSafe($k))
                    continue;

                if(isset($this->defaultClasses[$element->type]))
                {
                    // if we have default value
                    if(!array_key_exists('class',$element->attributes))
                    {
                        // but we have no attribute class defined
                        $element->attributes['class'] = $this->defaultClasses[$element->type]; // default will be set
                    }
                    elseif(is_array($element->attributes['class']))
                    {
                        // but if array defined
                        foreach ($element->attributes['class'] as $key => $param)
                        {
                            // every key
                            if(!strpos($param,'grid') && isset($this->defaultClasses[$element->type][$key])) //will be checked
                                $element->attributes['class'][$key] = $this->defaultClasses[$element->type][$key]; // and applied if exists not yet
                        }
                    }
                    elseif (!@strpos($element->attributes['class'],'grid'))
                    {
                    }
                }
                $elementLayout = $this->formInputLayout;
                $errorLayout = $this->formErrorLayout;
                $elementLayout = preg_replace('|{error}|',$errorLayout,$elementLayout);
                if ($error)
                    $elementLayout = preg_replace('|{error}|',$error,$elementLayout);
                $element->layout = $elementLayout;
                $elementOutput = $element->render();
                if ($error) {
                    $elementOutput = preg_replace('|\"form-row\"|','"form-row error"',$elementOutput);
                }
                $output .= $elementOutput;

                if(!empty($element->attributes['description']))
                    $output .= '<div class="forms__hint">' . $element->attributes['description'].'</div>';


            }
        }
        return $output;
    }

    public function renderButtons() {
        $output='';
        foreach($this->getButtons() as $button)
            $output.=$this->renderButton($button);
        return $output!=='' ? '<div class="form-row">'.$output.'</div>' : '';
    }

    public function renderButton($element) {
        $attrs = $element->attributes;
        $class = isset($attrs['class']) ? $attrs['class'] : '';
        $attrs['class'] = 'b-btn__submit';
        $element->attributes = $attrs;

        //$label = $element->label;
        //$element->label = '';
        $output='
                            <div>
								'.$element->render().'
							</div>
        ';
        return $output;
    }
}
?>