<?php
use \packages\base\translator;
use \packages\userpanel;
$this->the_header('login');
?>
<div class="box-change-passwd">
	<div class="errors">
		<?php
		$errorcode = $this->getErrorsHTML();
		if($errorcode){
		?>
		<div class="row">
			<div class="col-xs-12"><?php echo $errorcode; ?></div>
		</div>
		<?php } ?>
	</div>
	<form action="<?php echo userpanel\url('resetpwd/newpwd'); ?>" class="form-changepwd" method="POST">
		<h3><?php echo t("userpanel.newpwd.title"); ?></h3>
		<p><?php echo translator::trans('resetpwd.set.newpwd.description', ['user_name' => $this->user->getFullName()]); ?></p>
		<div class="errorHandler alert alert-danger no-display">
			<i class="fa fa-remove-sign"></i> <?php echo t("data_validation"); ?>
		</div>
		<fieldset>
			<?php $this->createField([
				'name' => 'password',
				'type' => 'password',
				'ltr' => true,
				'icon' => 'fa fa-key',
				'right' => true,
				'placeholder' => translator::trans('user.password')
			]); ?>
			<?php $this->createField([
				'name' => 'password2',
				'type' => 'password',
				'ltr' => true,
				'icon' => 'fa fa-key',
				'right' => true,
				'placeholder' => translator::trans('user.password_repeat')
			]); ?>
			<div class="form-actions">
				<button type="submit" class="btn btn-success pull-left">
					<i class="fa fa-unlock"></i> <?php echo translator::trans('resetpwd.confirm'); ?>
				</button>
			</div>
		</fieldset>
	</form>
</div>
<?php
$this->the_footer('login');