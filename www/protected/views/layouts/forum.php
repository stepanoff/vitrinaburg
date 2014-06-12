<?php $this->beginContent('application.views.layouts.v2');?>
<div id="inner-page">
  <div class="base-width clearfix">
    <div class="main-col">
      <?php $this->widget('application.widgets.VitrinaCrumbsWidget', array('items' => $this->crumbs)); ?>
      <div class="forum" id="forum">
		<?php echo $content; ?>
          </div>
        </div>
        <div class="left-col">
            <?php $this->widget('application.widgets.VitrinaLastCollectionsWidget', array()); ?>
            <div class="left-banner">
                <?php $this->renderPartial('application.views.blocks.banner_left', array()); ?>
            </div>
        </div>
      </div>
    </div>
<?php $this->endContent(); ?>