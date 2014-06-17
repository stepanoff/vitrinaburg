	<footer>
		<div class="inner">
			<div class="left">
				<script type="text/javascript">
				<?php
					$counters = $this->renderPartial('application.views.blocks.counters', array(), true);
				?>
				var counters = <?php echo CJSON::encode($counters); ?>;
				function reloadCounters () {
					$('#counters').html('');
					$(document).bind('beforedocwrite', function(event, data) {
						data.target = $('#counters');
					});
					document.write(counters);
				}
				</script>
				<div class="counters" id="counters">
					<?php echo $counters; ?>
				</div>
            <?php
                $this->widget('application.modules.VCb.components.VContentBlockWidget', array(
                    'namespace' => 'footer',
                    'name' => 'footerLeft',
                    'description' => 'Содержимое футера слева',
                ));
            ?>
			</div>
			
			<div class="right">
            <?php
                $this->widget('application.modules.VCb.components.VContentBlockWidget', array(
                    'namespace' => 'footer',
                    'name' => 'footerRight',
                    'description' => 'Содержимое футера справа',
                ));
            ?>
			</div>
			<div class="clr"></div>
		</div>
	
	</footer>
	
<?php
if (!$user)
	$this->widget('ext.VExtension.widgets.auth.VAuthWidget', array('action'=>'/site/login'));
?>
</body>
<script type="text/javascript">
$(document).ready(function() {
	$(".showMap").easy_map();
});
</script>
</html>