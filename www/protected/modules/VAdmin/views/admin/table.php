<?php
$this->widget('VAdminFilterBuilderWidget', array(
    'form' => $filterForm,
));
?>

<?php
    echo $list;
$this->widget('VAdminGridWidget', array(
    'dataProvider' => $dataProvider,
    'columns' => $columns,
));
?>
