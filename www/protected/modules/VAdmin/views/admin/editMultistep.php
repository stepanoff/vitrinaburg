<?php 
$cs=Yii::app()->clientScript;
$cs->registerScriptFile(Yii::app()->request->baseUrl.'/js/multistep_form.js', CClientScript::POS_END);
?>
<div class="adminForm siteForm" id="multiStepForm">
<div class="formStepNavigator" id="formStepNavigator">
<?
foreach ($attributes as $k=>$v)
{
	?>
	<div class="formStepTab" id="formStepItem<?php echo $k; ?>"><?php echo $v['name']; ?></div>
	<?php 
}
?>
</div>
<?php
foreach ($form->models as $m)
{
	echo CHtml::errorSummary($m, 'Исправьте ошибки:');
}

echo $form->renderBegin();

foreach ($attributes as $k=>$v)
{
	?>
	<div class="formStepForm" id="formStepForm<?php echo $k; ?>">
	<?php 
	foreach ($v['attributes'] as $attrName)
	{
		$element = $form->elements[$attrName];
		if (!$element)
			continue;
		if (get_class($element)!='CFormInputElement')
		{
			if ($element->model)
			{
				foreach ($element->getElements() as $_k=>$_element)
				{
					if (!$element->model->isAttributeSafe($_k))
						continue;
					$_element->layout = $this->formInputLayout;
					echo $_element->render ();	
				}
			}
		}
		else
		{
			if (!$form->model->isAttributeSafe($attrName))
				continue;
			$element->layout = $this->formInputLayout;
			echo $element->render ();	
		}
	}
	?>
	</div>
	<?php 
}
echo '<div class="buttons">'.$form->renderButtons().'</div>';
echo $form->renderEnd();			
?>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$("#multiStepForm").multistep_form();
});
</script>