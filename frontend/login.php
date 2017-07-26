<?php
use \packages\base;
use \packages\userpanel;
$this->the_header('login');
?>
<!-- start: LOGIN BOX -->
<div class="box-login">
	<h3>به حساب کاربری خود وارد شوید.</h3>
	<p>برای ورود لطفا ایمیل و کلمه عبور خود را وارد کنید.</p>
	<form class="form-login" name="form-login" action="<?php echo userpanel\url('login', array('ajax'=>1)); ?>" method="post">
		<?php
		$this->createField([
			'name' => 'backTo',
			'type' => 'hidden'
		]);
		?>
		<div class="errorHandler alert alert-danger no-display">
			<i class="fa fa-remove-sign"></i> اطلاعات وارد شده دارای مشکلاتی می باشد.
		</div>
		<fieldset>
			<div class="form-group">
				<span class="input-icon input-icon-right">
					<input type="text" class="form-control" name="username" placeholder="ایمیل یا تلفن همراه">
					<i class="fa fa-user"></i>
				</span>
			</div>
			<div class="form-group form-actions">
				<span class="input-icon input-icon-right">
					<input type="password" class="form-control password" name="password" placeholder="کلمه عبور">
					<i class="fa fa-lock"></i>
					<a class="forgot" href="<?php echo userpanel\url('resetpwd'); ?>">من کلمه عبورم را فراموش کردم</a>
				</span>
			</div>
			<div class="form-actions">
				<label for="remember" class="checkbox-inline"> 
					<input type="checkbox" class="grey remember" id="remember" name="remember" value="true">مرا به یاد داشته باش
				</label>
				<button type="submit" class="btn btn-bricky pull-left"><i class="fa fa-arrow-circle-left"></i> ورود</button>
			</div>
			<?php
			if($this->registerEnable){
			?>
			<div class="new-account">هنوز حساب کاربری ندارید؟ <a href="<?php echo userpanel\url('register');?>" class="register">به جمع ما بپیوندید!</a></div>
			<?php
			}
			?>
		</fieldset>
	</form>
</div>
<!-- end: LOGIN BOX -->
<?php $this->the_footer('login'); ?>
