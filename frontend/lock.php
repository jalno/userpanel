<?php
use function packages\userpanel\url;

$this->the_header('lock');
?>
<div class="box-ls">
	<img alt="" src="<?php echo $this->getSelfAvatarURL(); ?>"/>
	<div class="user-info">
		<?php
        if ($errorcode = $this->getErrorsHTML()) {
            echo $errorcode;
        }
?>
		<h1><i class="fa fa-lock"></i> <?php echo $this->getUser()->getFullName(); ?></h1>
		<span style="text-align: left;"><?php echo $this->getUserData('email'); ?></span>
		<span><em><?php echo t('lock.enterpassword'); ?></em></span>
		<form class="unlock" action="<?php echo url('lock'); ?>" method="post">
			<div class="input-group">
				<input type="password" placeholder="<?php echo t('user.password'); ?>" class="form-control" name="password">
				<span class="input-group-btn"><button class="btn btn-blue" type="submit"><i class="fa fa-chevron-left"></i></button> </span>
			</div>
			<div class="relogin">
				<a href="<?php echo url('logout'); ?>"><?php echo t('lock.logout', ['user_name' => $this->getUserData('name')]); ?></a>
			</div>
		</form>
	</div>
</div>
<?php
$this->the_footer('login');
