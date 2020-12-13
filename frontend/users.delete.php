<?php
use packages\base;
use packages\base\translator;
use packages\userpanel;

require_once("header.php");
?>
<!-- start: PAGE CONTENT -->
<form action="<?php echo userpanel\url('users/delete/'.$this->getDataForm('id')); ?>" method="POST" role="form" id="delete_form" class="form-horizontal">
	<div class="alert alert-block alert-warning fade in">
		<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> <?php echo translator::trans('user.delete.warning.title'); ?>!</h4>
		<p>
			<?php echo translator::trans("user.delete.warning", array(
				'user.id' => $this->getDataForm('id'),
				'user.name' => $this->getDataForm('name'),
				'user.email' => $this->getDataForm('email')
			)); ?>
		</p>
		<p>
			<a href="<?php echo userpanel\url('users'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-<?php echo Translator::getLang()->isRTL() ? "right" : "left"; ?>"></i> <?php echo t("userpanel.cancel"); ?></a>
			<button type="submit" class="btn btn-danger"><i class="fa fa-trash-o"></i> <?php echo t("userpanel.delete"); ?></button>
		</p>
	</div>
</form>

<?php
require_once('footer.php');
