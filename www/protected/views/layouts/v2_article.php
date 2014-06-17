<?php
	$this->renderPartial('application.views.blocks.head', array());
?>
	
	<section id="content">
			<div id="pageDescription">
				<?php
				if ($this->seoText) {
					echo $this->seoText;
				}
				?>
			</div>
			<script type="text/javascript">
				$("#pageDescription").hide();
			</script>
			<script type="text/javascript">
				$(document).ready(function() {
					$('#pageDescription').appendTo("#pageDescriptionFooter").show();
				});
			</script>
			<?php echo $content; ?>
	</section>
	
<?php
	$this->renderPartial('application.views.blocks.foot', array());
?>