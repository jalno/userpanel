<?php
use \packages\base\translator;
use \packages\userpanel;
$this->the_header('login');
?>
<div class="box-forgot">
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
	<form action="<?php echo userpanel\url('resetpwd', array('ajax'=>1)); ?>" class="form-resetpwd" method="POST">
		<h3><?php echo translator::trans('resetpwd.box.title'); ?></h3>
		<p>
		‍	<?php echo translator::trans('resetpwd.box.description'); ?>
		</p>
		<div class="alert alert-success no-display email-alert" role="alert">
			<strong>ایمیل ارسال شد . </strong> 
			<p>درخواست تغییر کلمه عبور شما موفقیت ثبت و با احراز هویت شما از طریق ایمیل تکمیل خواهد شد .</p>
		</div>
		<div class="errorHandler alert alert-danger no-display">
			<i class="fa fa-remove-sign"></i> اطلاعات وارد شده دارای مشکلاتی می باشد.
		</div>
		<fieldset>
			<?php $this->createField([
				'name' => 'method',
				'type' => 'radio',
				'inline' => true,
				'options' => [
					[
						'label' => translator::trans('user.sendemail'),
						'value' => 'email',
						'class' => 'grey'
					],
					[
						'label' => translator::trans('user.sendsms'),
						'value' => 'sms'
					]
				]
			]); ?>
			<?php $this->createField([
				'name' => 'username',
				'icon' => 'fa fa-phone',
				'right' => true,
				'placeholder' => translator::trans('resetpwd.username'),
				'ltr' => true
			]); ?>
			<div class="form-actions">
				<a class="btn btn-light-grey" href="<?php echo userpanel\url(); ?>">
					<i class="fa fa-arrow-circle-right "></i> <?php echo translator::trans('resetpwd.return'); ?>
				</a>
				<button type="submit" class="btn btn-bricky pull-left" <?php if($this->hasBlocked())echo "disabled"; ?>>
					<i class="fa fa-unlock-alt"></i> <?php echo translator::trans('resetpwd.recovery'); ?>
				</button>
			</div>
		</fieldset>
	</form>
	<form action="<?php echo userpanel\url('resetpwd/token/authentication', array('ajax'=>1)); ?>" class="form-authentication" method="POST">
		<h3><?php echo translator::trans('resetpwd.authentication.token.title'); ?></h3>
		<p>
		‍	<?php echo translator::trans('resetpwd.authentication.token.description'); ?>
		</p>
		<p class="cellphone text-center"></p>
		<p class="text-center">
			<a class="wrong-number" href="<?php echo userpanel\url('resetpwd', ['method' => 'sms']); ?>"><?php echo translator::trans('resetpwd.authentication.token.wrong-number'); ?></a>
		</p>
		<fieldset>
			<?php $this->createField([
				'name' => 'username',
				'type' => 'hidden'
			]); ?>
			<?php $this->createField([
				'name' => 'token',
				'icon' => 'fa fa-key',
				'right' => true,
				'placeholder' => translator::trans('resetpwd.token'),
				'ltr' => true,
				'type' => 'number'
			]); ?>
			<div class="form-actions">
				<button type="submit" class="btn btn-success pull-left">
					<i class="fa fa-check-square-o "></i> <?php echo translator::trans('resetpwd.confirm'); ?>
				</button>
			</div>
		</fieldset>
	</form>
</div>
<?php
$this->the_footer('login');