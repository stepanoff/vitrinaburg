		<div id="right">

            <?php
                $this->widget('application.modules.VCb.components.VContentBlockWidget', array(
                    'namespace' => '760_90',
                    'name' => '760_90Article',
                    'description' => 'Растяжка',
                ));
            ?>
			
			<h1>Статьи</h1>
			
			 
<?php
	if ($items) {
		echo '<ul id="items2">';

		foreach ($items as $item) {

			$imageTag = VHtml::thumb($item->img, array(250, false), VHtml::SCALE_WIDTH, array('alt'=>$item->title, 'title'=>$item->title));
			?>
				<li>
					<?php echo CHtml::link($imageTag, array('/vitrinaArticle/show', 'id'=>$item->id), array('class'=>'')); ?>
					<?php echo CHtml::link($item->title, array('/vitrinaArticle/show', 'id'=>$item->id), array('class'=>'')); ?>
					<span class="date"><?php echo Vhtml::formatDate($item->date); ?></span>
				</li>
			<?php
		}

		echo '</ul>';

	}
?>			

          <div id="pagi">
          <?php
            $this->widget('VitrinaLinkPagerWidget', array('pages'=>$pages));
          ?>
          </div>
          <?php
            $this->widget('application.widgets.VitrinaInfiniteScrollWidget', array(
            	'containerSelector' => '#content',
                'navSelector' => "#pagi",
                'nextSelector' => "#pagi a.next",
                'contentSelector' => "#items2",
                'itemSelector' => "#items2 li",
                'finishedMsg' => 'Изображения загружены',
                'msgText' => 'Загрузка изображений...',
            ));
          ?>
			
		</div>

		<aside>
			
            <?php
                $this->widget('application.modules.VCb.components.VContentBlockWidget', array(
                    'namespace' => 'skyscraper',
                    'name' => 'skyscraperArticle',
                    'description' => 'Баннер-небоскреб',
                ));
            ?>

            <?php
                $this->widget('application.modules.VCb.components.VContentBlockWidget', array(
                    'namespace' => 'socialBlock',
                    'name' => 'socialBlockArticle',
                    'description' => 'Блок с социалкой',
                ));
            ?>
			
            <?php
                $this->widget('application.widgets.VitrinaForumWidget', array(
                ));
            ?>

		</aside>			
