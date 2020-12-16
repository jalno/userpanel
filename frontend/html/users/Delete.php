<?php
use packages\base;
use packages\base\Translator;
use packages\userpanel;

$this->the_header();

$user = $this->getUser();
?>
<!-- start: PAGE CONTENT -->
<form action="<?php echo userpanel\url("users/delete/{$user->id}"); ?>" method="POST" role="form" id="delete_form" class="form-horizontal">
	<div class="alert alert-block alert-warning fade in">
		<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> <?php echo t('user.delete.warning.title'); ?>!</h4>
		<p>
			<?php echo t("user.delete.message", array(
				'user.id' => $user->id,
				'user.full_name' => $user->getFullName(),
			)); ?>
		</p>
		<p>
			<?php echo t($this->hasFatalError() ? "user.delete.resolve_fatal_errors" : "user.delete.warning"); ?>
		</p>
		<p>
			<a href="<?php echo userpanel\url('users'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-<?php echo Translator::getLang()->isRTL() ? "right" : "left"; ?>"></i> <?php echo t("userpanel.cancel"); ?></a>
			<?php if (!$this->hasFatalError()) { ?>
				<button type="submit" class="btn btn-danger"><i class="fa fa-trash-o"></i> <?php echo t("userpanel.delete"); ?></button>
			<?php } ?>
		</p>
	</div>
</form>

<?php
$this->the_footer();
