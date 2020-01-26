<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
	</main>

	<footer class="mdc-typography--subtitle2">
		<p class="footer__total"><?php echo $total ?> találat</p>
		<p class="footer__elapsed"><strong>{elapsed_time}</strong> mp</p>
	</footer>

	<script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
	<script type="text/javascript">
		window.mdc.autoInit();
	</script>
</body>
</html>
