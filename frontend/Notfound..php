<?php
use packages\base\Translator;

$this->the_header(!$this->loged_in ? 'logedout' : '');
?>
<div class="row">
	<!-- start: 404 -->
	<div class="col-sm-12 page-error">
		<div class="error-number teal">404</div>
		<div class="error-details col-sm-6 col-sm-offset-3">
			<h3><?php echo t('notfound.heading'); ?></h3>
			<p><?php echo t('notfound.description'); ?></p>
		</div>
	</div>
	<!-- end: 404 -->
</div>
<?php $this->the_footer(!$this->loged_in ? 'logedout' : ''); ?>
