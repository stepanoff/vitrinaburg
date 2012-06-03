<?php $this->widget('application.widgets.LinkPager',array('pages' => $pages)) ?>

<?php echo CHtml::link('Добавить', array('edit')); ?>
	
	<table width="100%" cellpadding="10" cellspacing="0">
		<tr>
			<th width="20%">Название</th>
			<th width="20%">Ссылка</th>
			<th width="10%">Показывать в меню</th>
			<th width="10%">Показывать в карте сайта</th>
			<th width="10%"></th>
			<th width="10%">Управление</th>
		</tr>
		<?php foreach ($list as $_k => $model) { ?>
		<tr style="border-bottom: 1px solid silver;">
			<td><?=$model->name?></td>
			
			<td>
				<?=$model->link?><br>
			</td>
			
			<td>
				<?php
				$this->widget(
					'admin.components.SwitchModerWidget',
					array(
						'id' => $model->id,
						'true' => ($model->inMenu),
						'false' => (!$model->inMenu),
						'trueAction' => 'addInMenu',
						'falseAction' => 'removeFromMenu'
					)
				);
				?>
			</td>
			
			<td>
				<?php
				$this->widget(
					'admin.components.SwitchModerWidget',
					array(
						'id' => $model->id,
						'true' => ($model->visible),
						'false' => (!$model->visible),
						'trueAction' => 'addOnSite',
						'falseAction' => 'removeFromSite'
					)
				);
				?>
			</td>
			
			<td><?php $this->widget('admin.components.SwitchWidget', array('list'=>array(
				array('name' => 'вверх', 'url' => array('sortUp', 'id' => $model->id), 'ajax' => true),
				array('name' => 'вниз', 'url' => array('sortDown', 'id' => $model->id), 'ajax' => true),
			)));  ?></td>
			
			<td style="border-bottom: 1px solid silver;"><?php $this->widget('admin.components.SwitchEditWidget', array('id' => $model->id));  ?></td>
			
		</tr>
		<?php } ?>
	</table>