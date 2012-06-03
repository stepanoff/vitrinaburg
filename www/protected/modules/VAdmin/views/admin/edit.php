<div class="adminForm siteForm">
<?
echo $form->renderBegin();
foreach ($form->models as $m)
{
	echo CHtml::errorSummary($m, 'Исправьте ошибки:');
}

foreach ($form->getElements() as $k=>$element)
{
	if (get_class($element)!='CFormInputElement')
	{
		if ($element->model)
		{
			foreach ($element->getElements() as $_k=>$_element)
			{
				$_element->layout = $this->formInputLayout;
				echo $_element->render ();	
			}
		}
	}
	else
	{
		$element->layout = $this->formInputLayout;
		echo $element->render ();	
	}
}

echo '<div class="buttons">'.$form->renderButtons().'</div>';
echo $form->renderEnd();			
?>
</div>
