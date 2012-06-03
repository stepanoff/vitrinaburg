	<?php $this->widget('application.widgets.LinkPager',array('pages' => $pages)) ?>

	<?php echo CHtml::link('Добавить', array('edit')); ?>
	
	<table width="100%" cellpadding="10" cellspacing="0">
		<tr>
			<th width="45%">Заголовок</th>
			<th width="45%">Путь</th>
			<th width="10%">Управление</th>
		</tr>
		<?php foreach ($list as $_k => $page) { ?>
		<tr style="border-bottom: 1px solid silver;">
			<td<?=($page->visible?'':' style="color: silver;"')?>>
				<?php echo $page->header ?>
			</td>
			<td>
				<?php echo $page->link ?>
			</td>			

			<td>
				<?php echo CHtml::link('Просмотр', array('/'.$page->link))?>
				<?php $this->widget('admin.components.SwitchEditWidget', array('id' => $page->id));  ?>
			</td>
		</tr>
		<?php } ?>
	</table>