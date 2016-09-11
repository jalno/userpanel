<?php
use \packages\base;
use \packages\base\frontend\theme;
?>
		<div class="copyright"><?php echo date('Y'); ?> &copy; clip-one by cliptheme.</div>
	</div>
	<!-- end: FOOTER -->
	<!--[if lt IE 9]>
	<script src="<?php echo theme::url('assets/plugins/respond.min.js'); ?>"></script>
	<script src="<?php echo theme::url('assets/plugins/excanvas.min.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo theme::url('assets/plugins/jquery/jquery-1.10.2.min.js'); ?>"></script>
	<![endif]-->
	<!--[if gte IE 9]><!-->
	<script src="<?php echo theme::url('assets/plugins/jquery/jquery-2.0.3.min.js'); ?>"></script>
	<!--<![endif]-->
	<?php $this->loadJS(); ?>
</body>
</html>
