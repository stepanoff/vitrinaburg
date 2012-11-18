	<?php /*$this->widget('application.widgets.LinkPager',array('pages' => $pages))*/ ?>

	<?php
		//echo CHtml::link('Добавить', array('edit')); 
	?>
	
	<table width="100%" cellpadding="10" cellspacing="0">
		<tr>
			<th width="80%">Описание</th>
			<th width="20%">Управление</th>
		</tr>
		<?php foreach ($list as $_k => $item) { ?>
		<tr style="border-bottom: 1px solid silver;">
			<td>
				<?php echo nl2br($item->description) ?>
			</td>
			<td><?php /*$this->widget('admin.components.SwitchEditWidget', array('id' => $item->id));*/  ?></td>
		</tr>
		<?php } ?>
	</table>