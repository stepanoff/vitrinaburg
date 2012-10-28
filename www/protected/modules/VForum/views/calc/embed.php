<div class="contentArea">
<?php $this->widget('ContentBlockWidget', array('name' => 'code_before')); ?>

<p class="code"><code>&lt;iframe src="http://<?php echo Yii::app()->params['domain']; ?>/framecalc" width="680" height="530" frameborder="no"&gt;&lt;/iframe&gt;</code></p>

<?php $this->widget('ContentBlockWidget', array('name' => 'code_after')); ?>
</div>