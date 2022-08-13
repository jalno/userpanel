<?php
use packages\base\translator;
use packages\userpanel;

?>
<form action="<?php echo userpanel\url('users/settings/'.$this->user->id); ?>" method="POST" role="form" id="settings_form">
<h3><?php echo t('profile.settings'); ?></h3>
	<hr>
<?php
$i = 0;
foreach($this->getSettings() as $tuning) {
	if ($i % 2 == 0) {
?>
	<div class="row">
<?php
	}
	$i++;
?>
		<div class="col-sm-6">
			<div class="panel panel-white settings-<?php echo $tuning->getName(); ?>">
				<div class="panel-heading">
					<i class="<?php echo ($tuning->getIcon() ?: 'fa fa-cogs'); ?>"></i>
				<?php echo t('titles.settings_'.$tuning->getName()); ?>
				</div>
				<div class="panel-body">
				<?php
				foreach($tuning->getFields() as $field){
					$this->createField($field);
				}
				?>
				</div>
			</div>
		</div>
<?php if ($i % 2 == 0) { ?>
	</div>
<?php
	}
}
if ($i % 2 != 0) {
?>
</div>
<?php } ?>
	<div class="row">
		<div class="col-md-offset-4 col-md-4">
			<button class="btn btn-success btn-block" type="submit"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("user.profile.save"); ?></button>
		</div>
	</div>
</form>