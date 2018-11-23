<?php
$this->the_header();
use \packages\base;
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\{user, user\socialnetwork};
?>
<!-- start: PAGE CONTENT -->
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="clip-user-plus"></i> <?php echo translator::trans('user.add'); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<form action="<?php echo userpanel\url('users/add/'); ?>" method="POST" role="form" id="add_form">
					<?php
					if($this->canEditPrivacy){
						foreach(array(
							'email',
							'cellphone',
							'phone',
							'socialnetworks_'.socialnetwork::telegram,
							'socialnetworks_'.socialnetwork::instagram,
							'socialnetworks_'.socialnetwork::skype,
							'socialnetworks_'.socialnetwork::twitter,
							'socialnetworks_'.socialnetwork::facebook,
							'socialnetworks_'.socialnetwork::gplus,
						) as $field){
							$this->createField(array(
								'type' => 'hidden',
								'name' => "visibility_".$field,
								'value' => true
							));

						}
					}
					?>
					<div class="row">
						<div class="col-md-12">
							<h3>اطلاعات پایه</h3>
							<hr>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?php
							$fields = array(
								array(
									'name' => 'name',
									'label' => translator::trans("user.name")
								),
								array(
									'name' => 'lastname',
									'label' => translator::trans("user.lastname")
								),
								array(
									'type' => 'email',
									'name' => 'email',
									'label' => translator::trans("user.email"),
									'error' => array(
										'data_duplicate' => 'user.email.data_duplicate'
									),
									'ltr' => true,
									'input-group' => $this->getFieldPrivacyGroupBtn('email')
								),
								array(
									'name' => 'phone',
									'label' => translator::trans("user.phone"),
									'ltr' => true,
									'input-group' => $this->getFieldPrivacyGroupBtn('phone')
								),
								array(
									'name' => 'cellphone',
									'label' => translator::trans("user.cellphone"),
									'error' => array(
										'data_duplicate' => 'user.cellphone.data_duplicate'
									),
									'ltr' => true,
									'input-group' => $this->getFieldPrivacyGroupBtn('cellphone')
								),
								array(
									'type' => 'password',
									'name' => 'password',
									'label' => translator::trans("user.password"),
									'value' => ''
								),
							);
							foreach($fields as $field){
								$this->createField($field);
							}
							?>
						</div>
						<div class="col-md-6">
							<?php

							$this->createField(array(
								'type' => 'select',
								'name' => 'type',
								'label' => translator::trans("user.type"),
								'options' => $this->getTypesForSelect()
							));
							?>
							<div class="row">
								<div class="col-md-4">
									<?php
									$this->createField(array(
										'type' => 'select',
										'name' => 'country',
										'label' => translator::trans("user.country"),
										'options' => $this->getCountriesForSelect(),
										'ltr' => true
									));
									?>
								</div>
								<div class="col-md-4">
									<?php
									$this->createField(array(
										'name' => 'city',
										'label' => translator::trans("user.city")
									));
									?>
								</div>
								<div class="col-md-4">
									<?php
									$this->createField(array(
										'type' => 'number',
										'name' => 'zip',
										'label' => translator::trans("user.zip")
									));
									?>
								</div>
							</div>
							<?php
							$this->createField(array(
								'name' => 'address',
								'label' => translator::trans("user.address")
							));
							$this->createField(array(
								'type' => 'radio',
								'name' => 'status',
								'label' => translator::trans("user.status"),
								'inline' => true,
								'options' => array(
									array(
										'label' => translator::trans("user.status.active"),
										'value' => user::active,
										'class' => 'grey'
									),
									array(
										'label' => translator::trans("user.status.suspend"),
										'value' => user::suspend,
										'class' => 'grey'
									),
									array(
										'label' => translator::trans("user.status.deactive"),
										'value' => user::deactive,
										'class' => 'grey'
									)
								)
							));
							$this->createField(array(
								'type' => 'number',
								'name' => 'credit',
								'label' => translator::trans("user.credit")
							));
							$this->createField(array(
								'type' => 'password',
								'name' => 'password2',
								'label' => translator::trans("user.password_repeat"),
								'value' => ''
							));
							?>

						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<h3>شبکه های اجتماعی</h3>
							<hr>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<?php
							$fields = array(
								array(
									'name' => 'socialnets['.socialnetwork::telegram.']',
									'placeholder' => "Telegram",
									'icon' => 'fa fa-telegram',
									'ltr' => true,
									'input-group' => $this->getFieldPrivacyGroupBtn('socialnetworks_'.socialnetwork::telegram)
								),
								array(
									'name' => 'socialnets['.socialnetwork::instagram.']',
									'placeholder' => "Instagram",
									'icon' => 'fa fa-instagram',
									'ltr' => true,
									'input-group' => $this->getFieldPrivacyGroupBtn('socialnetworks_'.socialnetwork::instagram)
								),
								array(
									'name' => 'socialnets['.socialnetwork::skype.']',
									'placeholder' => "Skype",
									'icon' => 'fa fa-skype',
									'ltr' => true,
									'input-group' => $this->getFieldPrivacyGroupBtn('socialnetworks_'.socialnetwork::skype)
								)
							);
							foreach($fields as $field){
								$this->createField($field);
							}
							?>
						</div>
						<div class="col-md-6">
							<?php
							$fields = array(
								array(
									'name' => 'socialnets['.socialnetwork::twitter.']',
									'placeholder' => "Twitter",
									'icon' => 'clip-twitter',
									'ltr' => true,
									'input-group' => $this->getFieldPrivacyGroupBtn('socialnetworks_'.socialnetwork::twitter)
								),
								array(
									'name' => 'socialnets['.socialnetwork::facebook.']',
									'placeholder' => "Facebook",
									'icon' => 'clip-facebook',
									'ltr' => true,
									'input-group' => $this->getFieldPrivacyGroupBtn('socialnetworks_'.socialnetwork::facebook)
								),
								array(
									'name' => 'socialnets['.socialnetwork::gplus.']',
									'placeholder' => "Google+",
									'icon' => 'fa fa-google-plus',
									'ltr' => true,
									'input-group' => $this->getFieldPrivacyGroupBtn('socialnetworks_'.socialnetwork::gplus)
								)
							);
							foreach($fields as $field){
								$this->createField($field);
							}
							?>
						</div>
					</div>
					<div class="row" style="margin-top: 20px;margin-bottom: 20px;">
						<div class="col-md-offset-4 col-md-4">
							<button class="btn btn-teal btn-block" type="submit"><i class="fa fa-arrow-circle-left"></i> <?php echo translator::trans("user.add"); ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
