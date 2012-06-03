<?php $this->widget('application.widgets.LinkPager',array('pages' => $pages)) ?>

<?php echo CHtml::link('Добавить', array('edit')); ?>
	
	<table width="100%" cellpadding="10" cellspacing="0">
		<tr>
			<th></th>
			<th width="30%">Имя</th>
			<th width="30%">Email / Логин / Пароль</th>
			<th width="20%">Роль</th>
			<th width="10%">Управление</th>
		</tr>
		<?php foreach ($list as $_k => $user) { ?>
		<tr style="border-bottom: 1px solid silver;">
			<td><?=!empty($user->photo)?'<div>'.CHtml::image(FileUtils::urlByUid($user->photo, 60)).'</div>':'';?></td>
			<td><?=$user->name?></td>
			
			<td>
				<?=$user->email?><br>
				<?=$user->user->username?><br>
				<?=$user->user->password?><br>
			</td>
			
			<td>
			<?php
			$this->widget(
				'admin.components.ListWidget',
				array(
					'id' => $user->id,
					'value' => $user->_role,
					'action' => 'role',
					'list' => AdminController::getAdminRoles(),
				)
			);
			?>
			</td>
			
			<td><?php $this->widget('admin.components.SwitchEditWidget', array('id' => $user->id));  ?></td>
		</tr>
		<?php } ?>
	</table>