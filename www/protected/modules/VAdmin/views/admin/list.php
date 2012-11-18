<?php /*$this->widget('admin.components.FilterWidget', array('controller' => $this));*/  ?>

<?php
$this->widget('VAdminGridWidget', array(
    'dataProvider'=>$dataProvider,
    'columns'=>array(
            'name',          // display the 'title' attribute
            array(            // display a column with "view", "update" and "delete" buttons
                'class'=>'CButtonColumn',
            ),
        ),
));
?>
