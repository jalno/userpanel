<?php
use packages\base;
use packages\userpanel;
$this->the_header('login');
$isRTL = (bool) base\translator::getLang()->isRTL();
?>
<!-- start: LOGIN BOX -->
<div class="box-login">
	<h3><?php echo t("userpanel.login.heading"); ?></h3>
	<p><?php echo t("userpanel.login.description"); ?></p>
	<form class="form-login" name="form-login" action="<?php echo userpanel\url("login"); ?>" method="post">
		<?php
		$this->createField([
			'name' => 'backTo',
			'type' => 'hidden'
		]);
		if ($errorcode = $this->getErrorsHTML()) {
			echo $errorcode;
		}
		?>
		<div class="errorHandler alert alert-danger no-display">
			<i class="fa fa-remove-sign"></i> <?php echo t("userpanel.login.incorrect"); ?>.
		</div>
		<fieldset>
			<div class="input-group floating-label-group credential-container">
				<input type="hidden" name="cellphone[number]">
				<span class="input-icon input-icon-right">
					<input type="text" name="credential" class="form-control" required>
					<span class="floating-label"><?php echo t("userpanel.login.email_or_phone"); ?></span>
					<i class="fa fa-user"></i>
				</span>
				<span class="input-group-btn form-group hidden">
					<select name="credential[code]" class="form-control"></select>
				</span>
			</div>

			<div class="form-group floating-label-group">
				<span class="input-icon input-icon-right">
					<input type="password" name="password" class="form-control" required />
					<span class="floating-label">کلمه عبور</span>
					<i class="fa fa-lock"></i>
					<a class="forgot" href="<?php echo userpanel\url('resetpwd'); ?>"><?php echo t("userpanel.login.forgot_password"); ?></a>
				</span>
			</div>

			<div class="form-actions">
				<label for="remember" class="checkbox-inline"> 
					<input type="checkbox" class="grey remember" id="remember" name="remember" value="true">
					<?php echo t("userpanel.login.remember_me"); ?>
				</label>
				<?php $direction =  ($isRTL) ? "left" : "right"; ?>
				<button type="submit" class="btn btn-bricky pull-<?php echo $direction; ?>">
					<?php echo t("userpanel.login.signin_btn"); ?>
					<i class="fa fa-arrow-circle-<?php echo $direction; ?>"></i>
				</button>
			</div>
			<?php
			if($this->registerEnable){
			?>
			<div class="new-account"> <?php echo t("userpanel.login.register.text"); ?> <a href="<?php echo userpanel\url('register');?>" class="register"><?php echo t("userpanel.login.register.link"); ?></a></div>
			<?php
			}
			?>
		</fieldset>
	</form>
</div>
<!-- end: LOGIN BOX -->
<?php $this->the_footer('login'); ?>
