<?php
use packages\base\frontend\theme;
use packages\userpanel;
use packages\userpanel\{User, Date};
use themes\clipone\utility;
?>
<div class="row">
	<div class="col-sm-5 col-md-4">
		<div class="user-left">
			<div class="center">
				<h4><?php echo $this->getData('user')->getFullName(); ?></h4>
				<form class="user_image"action="<?php echo userpanel\url('users/edit/'.$this->getUserData('id')); ?>" method="post">
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
							'label label-inverse' => user::deactive,
							'label label-success' => user::active,
							'label label-warning' => user::suspend
						));
						$statusTxt = utility::switchcase($this->getUserData('status'), array(
							'user.status.deactive' => user::deactive,
							'user.status.active' => user::active,
							'user.status.suspend' => user::suspend
						));
						?>
						<span class="<?php echo $statusClass; ?>"><?php echo t($statusTxt); ?></span>
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