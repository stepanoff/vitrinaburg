<!DOCTYPE html>
<html>
  <head>
	<script type="text/javascript">
		<?php
        if ($changeHash)
        {
			$code = 'if (window.opener) {';
			$code .= 'window.close();';
			if ($redirect)
				$code .= 'window.opener.location.hash = \''.addslashes($url).'\';';
			$code .= '}';
			$code .= 'else {';
			if ($redirect)
				$code .= 'window.location.hash = \''.addslashes($url).'\';';
			$code .= '}';
			echo $code;
        }
        else
        {
            $code = 'if (window.opener) {';
            $code .= 'window.close();';
            if ($redirect)
                $code .= 'window.opener.location.href = \''.addslashes($url).'\';';
            $code .= '}';
            $code .= 'else {';
            if ($redirect)
                $code .= 'window.location.href = \''.addslashes($url).'\';';
            $code .= '}';
            echo $code;
        }
		?>
	</script>
  </head>
  <body>
	<h2 id="title" style="display:none;">Redirecting back to the application...</h2>
	<h3 id="link"><a href="<?php echo $url; ?>">Click here to return to the application.</a></h3>
	<script type="text/javascript">
		document.getElementById('title').style.display = '';
		document.getElementById('link').style.display = 'none';
	</script>
  </body>
</html>