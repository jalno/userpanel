<?php
use \packages\base\frontend\theme;
use \packages\base\translator;
use \packages\userpanel;
$this->the_header('lock');
?>
<div class="box-ls">
	<img alt="" src="<?php echo theme::url('assets/images/avatar-1-xl.jpg'); ?>"/>
	<div class="user-info">
		<h1><i class="fa fa-lock"></i> <?php echo $this->getUserData('name') .' '.$this->getUserData('lastname'); ?></h1>
		<span style="text-align: left;"><?php echo $this->getUserData('email'); ?></span>
		<span><em><?php echo translator::trans('lock.enterpassword'); ?></em></span>
		<form class="unlock" action="<?php echo userpanel\url('lock'); ?>" method="post">
			<div class="input-group">
				<input type="password" placeholder="<?php echo translator::trans('user.password'); ?>" class="form-control" name="password">
				<span class="input-group-btn"><button class="btn btn-blue" type="submit"><i class="fa fa-chevron-left"></i></button> </span>
			</div>
			<div class="relogin">
				<a href="<?php echo userpanel\url('logout'); ?>"><?php echo translator::trans('lock.logout', array('user_name' => $this->getUserData('name'))); ?></a>
			</div>
		</form>
	</div>
</div>
<?php $this->the_footer('login'); ?>
