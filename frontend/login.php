<?php
use \packages\base;
use \packages\base\frontend\theme;

$this->addJSFile(theme::url('assets/plugins/jquery-validation/dist/jquery.validate.min.js'));
$this->addJSFile(theme::url('assets/js/login.js'));
$this->addJS('jQuery(document).ready(function() {Login.init();});');

?><!DOCTYPE html>
<!--[if IE 8]><html class="ie8 no-js" lang="en"><![endif]-->
<!--[if IE 9]><html class="ie9 no-js" lang="en"><![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
	<!--<![endif]-->
	<!-- start: HEAD -->
	<head>
		<title><?php echo $this->getTitle(); ?></title>
		<meta charset="utf-8" />
		<!--[if IE]><meta http-equiv='X-UA-Compatible' content="IE=edge,IE=9,IE=8,chrome=1" /><![endif]-->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<?php
		$description = $this->getDescription();
		if($description){
			echo("<meta content=\"{$description}\" name=\"description\" />");
		}
		$this->loadCSS();
		?>
		<!--[if IE 7]>
		<link rel="stylesheet" href="<?php echo theme::url('assets/plugins/font-awesome/css/font-awesome-ie7.min.css'); ?>">
		<![endif]-->
	</head>
	<!-- end: HEAD -->
	<!-- start: BODY -->
	<body class="login example1 rtl">
		<div class="main-login col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
			<div class="logo">CLIP<i class="clip-clip"></i>ONE
			</div>
			<!-- start: LOGIN BOX -->
			<div class="box-login">
				<h3>به حساب کاربری خود وارد شوید.</h3>
				<p>برای ورود لطفا ایمیل و کلمه عبور خود را وارد کنید.</p>
				<form class="form-login" name="form-login" action="<?php echo base\url('userpanel/login', array('ajax'=>1)); ?>" method="post">
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
								<a class="forgot" href="#">من کلمه عبورم را فراموش کردم</a>
							</span>
						</div>
						<div class="form-actions">
							<label for="remember" class="checkbox-inline">
								<input type="checkbox" class="grey remember" id="remember" name="remember">مرا به یاد داشته باش
							</label>
							<button type="submit" class="btn btn-bricky pull-left"><i class="fa fa-arrow-circle-left"></i> ورود</button>
						</div>
						<?php
						if($this->registerEnable){
						?>
						<div class="new-account">هنوز حساب کاربری ندارید؟ <a href="<?php echo base\url('userpanel/register');?>" class="register">به جمع ما بپیوندید!</a></div>
						<?php
						}
						?>
					</fieldset>
				</form>
			</div>
			<!-- end: LOGIN BOX -->
			<!-- start: FORGOT BOX -->
			<div class="box-forgot">
				<h3>Forget Password?</h3>
				<p>
					Enter your e-mail address below to reset your password.
				</p>
				<form class="form-forgot">
					<div class="errorHandler alert alert-danger no-display">
						<i class="fa fa-remove-sign"></i> You have some form errors. Please check below.
					</div>
					<fieldset>
						<div class="form-group">
							<span class="input-icon">
								<input type="email" class="form-control" name="email" placeholder="Email">
								<i class="fa fa-envelope"></i> </span>
						</div>
						<div class="form-actions">
							<a class="btn btn-light-grey go-back">
								<i class="fa fa-circle-arrow-left"></i> Back
							</a>
							<button type="submit" class="btn btn-bricky pull-left">
								Submit <i class="fa fa-arrow-circle-right"></i>
							</button>
						</div>
					</fieldset>
				</form>
			</div>
			<!-- end: FORGOT BOX -->

			<div class="copyright"><?php echo date('Y'); ?> &copy; clip-one by cliptheme.</div>
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
