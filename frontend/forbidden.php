<?php
use \packages\base\frontend\theme;
use \packages\base\translator;
use \packages\userpanel;
$this->the_header(!$this->loged_in ? 'logedout' : '');
?>
<div class="page-error">
	<div class="error-number bricky">403</div>
	<div class="error-details col-sm-6 col-sm-offset-3">
		<h3><?php echo translator::trans('forbidden.heading'); ?></h3>
		<p><?php echo translator::trans('forbidden.description'); ?></p>
	</div>
</div>
<?php $this->the_footer(!$this->loged_in ? 'logedout' : ''); ?>
