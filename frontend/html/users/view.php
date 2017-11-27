<?php
$this->the_header();
use \packages\base\frontend\theme;
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\user;
use \themes\clipone\utility;
?>
<!-- start: PAGE CONTENT -->
<div class="row">
	<div class="col-sm-12">
		<div class="tabbable">
			<ul class="nav nav-tabs tab-padding tab-space-3 tab-blue">
				<li class="active"><a data-toggle="tab" href="<?php echo userpanel\url('users/view/'.$this->getUserData('id')); ?>"><?php echo translator::trans("user.profile.overview"); ?></a></li>
				<li><a href="<?php echo userpanel\url('users/edit/'.$this->getUserData('id')); ?>">ویرایش اطلاعات</a></li>
				<?php if($this->canEditSettings() and $this->getSettings()){ ?>
				<li><a href="<?php echo userpanel\url('users/settings/'.$this->getUserData('id')); ?>"><?php echo translator::trans("profile.settings"); ?></a></li>
				<?php } ?>
			</ul>
			<div class="tab-content">
				<div id="panel_overview" class="tab-pane in active">
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
											<th colspan="3">اطلاعات تماس</th>
										</tr>
									</thead>
									<tbody>
										<?php
										if($web){
										?>
										<tr>
											<td>تارنما:</td>
											<td>
											<a href="http://<?php echo $this->getUserData('web'); ?>" target="_blank">www.<?php echo $this->getUserData('web'); ?></a></td>
											<td><a href="<?php echo userpanel\url('users/edit/'.$this->getUserData('id')); ?>"><i class="fa fa-pencil edit-user-info"></i></a></td>
										</tr>
										<?php
										}
										if($email){
										?>
										<tr>
											<td>رایانامه:</td>
											<td><?php echo $this->getUserData('email'); ?></td>
											<td><a href="<?php echo userpanel\url('email/send/', array('user' => $this->getUserData('id'))); ?>"><i class="fa fa-envelope-o edit-user-info"></i></a></td>
										</tr>
										<?php
										}
										if($phone){
										?>
										<tr>
											<td>تلفن:</td>
											<td><?php echo $this->getUserData('phone'); ?></td>
											<td></td>
										</tr>
										<?php
										}
										if($cellphone){
										?>
										<tr>
											<td>تلفن همراه:</td>
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
							<p>ما سعی میکنیم که تمامی اطلاعات کاربران را ذخیره و نگهداری کنیم.در ادامه تعدادی از آخرین فعالیت های کاربر قابل مشاهده است</p>

							<div class="panel panel-white">
								<div class="panel-heading">
									<i class="clip-menu"></i>آخرین فعالیت ها
									<div class="panel-tools">
										<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
									</div>
								</div>
								<div class="panel-body panel-scroll" style="height:300px">
									<ul class="activities">
									<?php
									foreach($this->loadLogs() as $log){
										$lHandler = $log->getHandler();
									?>
									<li>
										<a class="activity" href="<?php echo $this->canViewLog() ? userpanel\url("logs/view/{$log->id}") : "#"; ?>">
											<i class="circle-icon <?php echo "{$lHandler->getIcon()} {$lHandler->getColor()}"; ?>"></i>
											<span class="desc"><?php echo $log->title; ?></span>
											<div class="time">
												<i class="fa fa-time bigger-110"></i><?php echo utility::dateFormNow($log->time); ?>
											</div>
										</a>
									</li>
									<?php
									}
									?>
									</ul>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
