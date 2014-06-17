		<div id="right">

            <?php
                $this->widget('application.modules.VCb.components.VContentBlockWidget', array(
                    'namespace' => '760_90',
                    'name' => '760_90Article',
                    'description' => 'Растяжка',
                ));
            ?>
			
			<div id="date">15 Января</div>
			
			<h1><?php echo $item->title; ?></h1>
			
			<article>
				<div class="like">
	            <?php
	                $this->widget('application.widgets.VitrinaShareWidget', array(
	                ));
	            ?>
				</div>
			
				<div class="text-art">
				
					<?php echo $item->text; ?>

				</div>
			
			</article>
			
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

			<!--div id="adv2">
				<a href="#"><img src="img/b1.png" alt="name"></a>
			</div>
			<div id="social">
				<img src="img/s.png" alt="name">
			</div-->
			
			<div id="forum">
				<div class="item">
					<a href="#">Форум</a> <span>800 тем</span>
				</div>
				<div class="item">
					<a href="#">Покупка вещей через интернет-магазин</a> <span>22</span>
				</div>
				<div class="item">
					<a href="#">Безвкусица</a> <span>22</span>
				</div>
				<div class="item">
					<a href="#">Безвкусица</a> <span>22</span>
				</div>
				<div class="item">
					<a href="#">Безвкусица</a> <span>22</span>
				</div>
				<div class="item">
					<a href="#">Безвкусица</a> <span>22</span>
				</div>
			</div>
			
		</aside>