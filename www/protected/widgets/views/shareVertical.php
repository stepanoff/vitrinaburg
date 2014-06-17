<style type="text/css">
.b-share_theme_counter .b-share-btn__wrap {
	position: relative;
	float: none !important;
	margin: 0 0 5px 0 !important;
	display: block !important;
}
.b-share__handle {
	float: none !important;
}
</style>

<div id="shareDiv"></div>
<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
<script type="text/javascript">
$(document).ready(function(){
	new Ya.share({
	        element: 'shareDiv',
	            theme: 'counter',
	            elementStyle: {
	                'type': 'none',
	                'border': false,
	                'quickServices': ['<?php echo implode("', '", $services); ?>']
	            },
	            link: '<?php echo $options['link']; ?>',
	            title: '<?php echo $options['linkText']; ?>',
	            description: '<?php echo $options['annotation']; ?>',
	            image: '<?php echo $options['imageUrl']; ?>',
	            serviceSpecific: {
	                twitter: {
	                    title: '<?php echo $options['linkText']; ?>'
	               }
	        }
		});
});
</script>