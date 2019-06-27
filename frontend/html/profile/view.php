<?php
$this->the_header();
use \packages\base\frontend\theme;
use \packages\userpanel;
use \packages\userpanel\user;
use \packages\base;
use \packages\base\translator;
use \themes\clipone\utility;
?>

<div class="tabbable">
	<ul class="nav nav-tabs tab-padding tab-space-3 tab-blue">
		<li class="active"><a data-toggle="tab" href="<?php echo userpanel\url('profile/view'); ?>"><?php echo translator::trans("profile.view"); ?></a></li>
		<li><a href="<?php echo userpanel\url('profile/edit'); ?>"><?php echo translator::trans("profile.edit"); ?></a></li>
		<?php if($this->canEditSettings() and $this->getSettings()){ ?>
		<li><a href="<?php echo userpanel\url('profile/settings'); ?>"><?php echo translator::trans("profile.settings"); ?></a></li>
		<?php } ?>
	</ul>
	<div class="tab-content">
		<div id="panel_overview" class="tab-pane in active">
			<div class="row">
				<div class="col-sm-5 col-md-4">
					<div class="user-left">
						<div class="center">
							<h4><?php echo $this->getData('user')->getFullName(); ?></h4>
							<form class="profile_image"action="<?php echo userpanel\url('profile/edit'); ?>" method="post">
								<div class="fileupload fileupload-new" data-provides="fileupload">
									<div class="form-group">
										<div class="user-image avatarPreview">
											<img src="<?php echo $this->getAvatarURL(); ?>" class="preview img-responsive">
											<input name="avatar" type="file">
											<div class="button-group">
												<button type="button" class="btn btn-teal btn-sm btn-upload"><i class="fa fa-pencil"></i></button>
												<button type="button" class="btn btn-bricky btn-sm btn-remove" data-default="<?php echo theme::url('assets/images/defaultavatar.jpg'); ?>"><i class="fa fa-times"></i></button>
											</div>
										</div>
									</div>
								</div>
							</form>
							<?php
							if($this->networks){
							?>
							<hr>
							<p>
							<?php
								foreach($this->networks as $network => $url){
									echo("<a href=\"{$url}\" target=\"_blank\" class=\"btn btn-{$network} btn-sm btn-squared\"><i class=\"fa fa-{$network}\"></i></a> ");
								}
							?>
							</p>
							<?php } ?>
							<hr>
						</div>
						<?php
						$web = ($this->getUserData('web') and $this->is_public('web'));
						$phone = ($this->getUserData('phone') and $this->is_public('phone'));
						$email = $this->is_public('email');
						$cellphone = $this->is_public('cellphone');
						if($web or $phone or $email or $cellphone){
						?>
						<table class="table table-condensed table-hover">
							<thead>
								<tr>
									<th colspan="3">اطلاعات تماس</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if($web){
								?>
								<tr>
									<td>تارنما:</td>
									<td><a href="http://<?php echo $this->getUserData('web'); ?>" target="_blank">www.<?php echo $this->getUserData('web'); ?></a></td>
								</tr>
								<?php
								}
								if($email){
								?>
								<tr>
									<td>رایانامه:</td>
									<td><?php echo $this->getUserData('email'); ?></td>
								</tr>
								<?php
								}
								if($phone){
								?>
								<tr>
									<td>تلفن:</td>
									<td><?php echo $this->getUserData('phone'); ?></td>

								</tr>
								<?php
								}
								if($cellphone){
								?>
								<tr>
									<td>تلفن همراه:</td>
									<td><?php echo $this->getUserData('cellphone'); ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<?php } ?>
						<table class="table table-condensed table-hover">
							<thead>
								<tr>
									<th colspan="3">اطلاعات کلی</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>سطح کاربری</td>
									<td><?php echo $this->getUserData('type')->title; ?></td>
									<td></td>
								</tr>
								<tr>
									<td>آخرین فعالیت</td>
									<td><?php echo utility::dateFormNow($this->getUserData('lastonline')); ?></td>
									<td></td>
								</tr>
								<tr>
									<td>آخرین ورود</td>
									<td><?php echo($this->lastlogin ? utility::dateFormNow($this->lastlogin) : translator::trans('user.lastlogin.never')) ; ?></td>
									<td></td>
								</tr>
								<tr>
									<td>وضعیت</td>
									<td>
									<?php
									$statusClass = utility::switchcase($this->getUserData('status'), array(
										'label label-inverse' => user::deactive,
										'label label-success' => user::active,
										'label label-warning' => user::suspend
									));
									$statusTxt = utility::switchcase($this->getUserData('status'), array(
										'deactive' => user::deactive,
										'active' => user::active,
										'suspend' => user::suspend
									));
									?>
									<span class="<?php echo $statusClass; ?>"><?php echo translator::trans($statusTxt); ?></span>
									</td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-sm-7 col-md-8">
					<?php echo $this->buildBoxs(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
