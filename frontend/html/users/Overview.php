<?php
use packages\base\frontend\theme;
use packages\userpanel;
use packages\userpanel\{User, Date, authentication};
use themes\clipone\utility;
?>
<div class="row">
	<div class="col-sm-5 col-md-4">
		<div class="user-left">
			<div class="center">
				<h4><?php echo $this->getData('user')->getFullName(); ?></h4>
				<form class="user_image" action="<?php echo userpanel\url('users/edit/'.$this->getUserData('id')); ?>" method="post">
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
			<?php if ($this->canEdit or $this->canLogin) { ?>
				<hr>
				<div class="admin-actions">
				<?php 
				$me = authentication::getID();
				if ($this->canLogin and $this->getUserData('id') != $me) { ?>
					<a data-toggle="modal" href="#user-login" class="btn btn-info tooltips" type="button" data-user="<?php echo $id; ?>">
						<div class="btn-icons">
							<i class="fa fa-user-secret"></i>
						</div>
						<?php echo t('userpanel.user.login'); ?>
					</a>
				<?php
				}
				if ($this->canEdit) {
				$status = $this->getUserData('status');
				$id = $this->getUserData('id');
				if ($status == User::active) {
				?>
					<button class="btn btn-warning btn-suspend-user" type="button" data-user="<?php echo $id; ?>">
						<div class="btn-icons">
							<i class="fa fa-user-times"></i>
						</div>
					<?php echo t('userpanel.user.suspend'); ?>
					</button>
				<?php } else { ?>
					<button class="btn btn-success btn-active-user" type="button" data-user="<?php echo $id; ?>">
						<div class="btn-icons">
							<i class="fa fa-check-square"></i>
						</div>
					<?php echo t('userpanel.user.activate'); ?>
					</button>
			<?php
				}
			}
			?>
				</div>
			<?php
			}
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
						<th colspan="3"><?php echo t("userpanel.profile.contact_info"); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($web){
					?>
					<tr>
						<td><?php echo t("userpanel.profile.web"); ?>:</td>
						<td>
						<a href="http://<?php echo $this->getUserData('web'); ?>" target="_blank">www.<?php echo $this->getUserData('web'); ?></a></td>
						<td><a href="<?php echo userpanel\url('users/edit/'.$this->getUserData('id')); ?>"><i class="fa fa-pencil edit-user-info"></i></a></td>
					</tr>
					<?php
					}
					if($email){
					?>
					<tr>
						<td><?php echo t("user.email"); ?>:</td>
						<td><?php echo $this->getUserData('email'); ?></td>
						<td><a href="<?php echo userpanel\url('email/send/', array('user' => $this->getUserData('id'))); ?>"><i class="fa fa-envelope-o edit-user-info"></i></a></td>
					</tr>
					<?php
					}
					if($phone){
					?>
					<tr>
						<td><?php echo t("user.phone"); ?>:</td>
						<td><?php echo $this->getUserData('phone'); ?></td>
						<td></td>
					</tr>
					<?php
					}
					if($cellphone){
					?>
					<tr>
						<td><?php echo t("user.cellphone"); ?>:</td>
						<td><?php echo $this->getUserData('cellphone'); ?></td>
						<td><a href="<?php echo userpanel\url('sms/send/', array('user' => $this->getUserData('id'))); ?>" ><i class="clip-mobile-3 edit-user-info"></i></a></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php } ?>
			<table class="table table-condensed table-hover">
				<thead>
					<tr>
						<th colspan="3"><?php echo t("userpanel.profile.general_info"); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo t("user.type"); ?></td>
						<td><?php echo $this->getUserData('type')->title; ?></td>
						<td></td>
					</tr>
					<tr>
						<td><?php echo t("userpanel.profile.register_date"); ?></td>
						<td><?php echo Date::relativeTime($this->getUserData('registered_at')); ?></td>
						<td></td>
					</tr>
					<tr>
						<td><?php echo t("userpanel.profile.last_activity"); ?></td>
						<td><?php echo Date::relativeTime($this->getUserData('lastonline')); ?></td>
						<td></td>
					</tr>
					<tr>
						<td><?php echo t("userpanel.profile.last_login"); ?></td>
						<td><?php echo($this->lastlogin ? Date::relativeTime($this->lastlogin) : t('user.lastlogin.never')) ; ?></td>
						<td></td>
					</tr>
					<tr>
						<td><?php echo t("user.credit"); ?></td>
						<td class="user-credit">
							<span class="ltr"><?php echo number_format($this->getUserData("credit")); ?></span>
							<?php echo ' ' . $this->getUserCurrency(); ?>
						</td>
						<td></td>
					</tr>
					<tr>
						<td><?php echo t("user.status"); ?></td>
						<td>
						<?php
						$statusClass = utility::switchcase($this->getUserData('status'), array(
							'label-inverse' => user::deactive,
							'label-success' => user::active,
							'label-warning' => user::suspend
						));
						$statusTxt = utility::switchcase($this->getUserData('status'), array(
							'user.status.deactive' => user::deactive,
							'user.status.active' => user::active,
							'user.status.suspend' => user::suspend
						));
						?>
							<span class="label user-status-container <?php echo $statusClass; ?>"><?php echo t($statusTxt); ?></span>
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
<?php if ($this->canLogin) { ?>
	<div class="modal fade" id="user-login" tabindex="-1" data-show="true" role="dialog">
		<div class="modal-header">
			<h4 class="modal-title"><i class="fa fa-user-secret"></i>  <?php echo t('userpanel.user.login'); ?></h4>
		</div>
		<div class="modal-body">
			<form id="login-as-user" action="<?php echo userpanel\url('loginasuser/'.$this->getUserData('id')); ?>" method="POST" class="form-horizontal">
				<span><?php echo t('userpanel.user.login.confirm', ['user-name' => $this->getData('user')->getFullName()]); ?></span>
			</form>
		</div>
		<div class="modal-footer">
			<button type="submit" form="login-as-user" class="btn btn-success"><?php echo t("userpanel.submit"); ?></button>
			<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo t("userpanel.cancel"); ?></button>
		</div>
	</div>
<?php } ?>