<div>
<?php 
	$url = CHtml::normalizeUrl(array($action));
	echo CHtml::form($url);
	echo CHtml::hiddenField('id',$id);
	$params_ = array($key=>'js:$("#'.$elId.'").val()', 'id'=>$id);
	if (count($params))
		$params_ = $params_+$params;
	if (is_array($params) && count($params))
	{
		foreach ($params as $k=>$v)
		{
			echo CHtml::hiddenField($k,$v);
		}
	}
	echo $inputElement;
	$btn_opts = array('value'=>'Сменить');
	if ($ajax)
		$btn_opts['onclick'] = 'SendRequest("'.$url.'", '.CJavaScript::encode($params_).');return false;';
	echo CHtml::submitButton('', $btn_opts);
	echo CHtml::endForm();
?>
</div>