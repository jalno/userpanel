<?php
use \packages\base;
use \packages\base\frontend\theme;
?>
				</div>
			</div>
			<!-- end: PAGE -->
		</div>
		<!-- end: MAIN CONTAINER -->
		<!-- start: FOOTER -->
		<div class="footer clearfix">
			<div class="footer-inner">
				2014 &copy; clip-one by cliptheme.
			</div>
			<div class="footer-items">
				<span class="go-top"><i class="clip-chevron-up"></i></span>
			</div>
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
