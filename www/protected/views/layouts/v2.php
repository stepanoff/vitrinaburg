<?php
	$this->renderPartial('application.views.blocks.head', array());
?>
	
	<section id="content">
		<div style="padding: 20px 40px;" id="<?php echo $this->mainPage ? 'main-page' : 'inner-page'; ?>">
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
		</div>
	</section>
	
<?php
	$this->renderPartial('application.views.blocks.foot', array());
?>