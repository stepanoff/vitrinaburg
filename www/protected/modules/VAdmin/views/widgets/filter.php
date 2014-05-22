<div id="filter">
	<?php echo CHtml::beginForm('', 'get') ?>
	
	<table width="100%" cellpadding="5" cellspacing="0" class="filters">
		<tr>
			<td class="inputs">
				<?php foreach ($filters as $filter) { ?>
				
				<div class="filter">
					<small><?=$filter['label']?></small><br>
					<?=$filter['field']?>
				</div>
				<?php } ?>
			</td>
			<td class="buttons">
				<?php echo CHtml::submitButton('Поиск') ?>
				<?php echo CHtml::submitButton('Сброс', array('name' => 'filter[reset]')) ?>
			</td>		
		</tr>
	</table>
	
	<?php echo CHtml::endForm() ?>
</div>