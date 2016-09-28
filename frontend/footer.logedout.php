<?php
use \packages\base;
use \packages\base\frontend\theme;
?>
		</div>
		<!-- end: PAGE -->
		<!--[if lt IE 9]>
		<script src="<?php echo theme::url('assets/plugins/respond.min.js'); ?>"></script>
		<script src="<?php echo theme::url('assets/plugins/excanvas.min.js'); ?>"></script>
		<script type="text/javascript" src="<?php echo $this->source->url('assets/plugins/jquery/jquery-1.10.2.min.js'); ?>"></script>
		<![endif]-->
		<!--[if gte IE 9]><!-->
		<?php // <script src="<?php echo ;"></script> ?>
		<!--<![endif]-->
		<?php $this->loadJS(); ?>
	</body>
</html>
