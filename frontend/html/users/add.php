<?php
use packages\base\{Translator};
use packages\userpanel\{User, user\SocialNetwork};
use function packages\userpanel\url;

$this->the_header();
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<i class="clip-user-plus"></i> <?php echo t('user.add'); ?>
		<div class="panel-tools">
			<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
		</div>
	</div>
	<div class="panel-body">
		<form action="<?php echo url('users/add'); ?>" method="POST" role="form" id="add_form">
			<?php
			if ($this->canEditPrivacy) {
				foreach (array(
					'email',
					'cellphone',
					'phone',
					'socialnetworks_'.socialnetwork::telegram,
					'socialnetworks_'.socialnetwork::instagram,
					'socialnetworks_'.socialnetwork::skype,
					'socialnetworks_'.socialnetwork::twitter,
					'socialnetworks_'.socialnetwork::facebook,
					'socialnetworks_'.socialnetwork::gplus,
				) as $field) {
					$this->createField(array(
						'type' => 'hidden',
						'name' => "visibility_{$field}",
						'value' => true
					));
				}
			}
			?>
			<h3><?php echo t("userpanel.profile.general_info"); ?></h3>
			<hr>
			<div class="row">
				<div class="col-md-6">
					<?php
					$fields = array(
						array(
							'name' => 'name',
							'label' => t("user.name")
						),
						array(
							'name' => 'lastname',
							'label' => t("user.lastname")
						),
						array(
							'type' => 'email',
							'name' => 'email',
							'label' => t("user.email"),
							'error' => array(
								'data_duplicate' => 'user.email.data_duplicate'
							),
							'ltr' => true,
							'input-group' => $this->getInputGroupArrayFor('email'),
						),
						array(
							'name' => 'phone[number]',
							'label' => t("user.phone"),
							'ltr' => true,
							'input-group' => $this->getInputGroupArrayFor('phone'),
						),
						array(
							'name' => 'cellphone[number]',
							'label' => t("user.cellphone"),
							'error' => array(
								'data_duplicate' => 'user.cellphone.data_duplicate'
							),
							'ltr' => true,
							'input-group' => $this->getInputGroupArrayFor('cellphone'),
						),
					);
					if ($this->canChangeCredit) {
						$fields = array_merge($fields, array(
							array(
								'type' => 'password',
								'name' => 'password',
								'label' => t("user.password"),
								'value' => ''
							),
						));
					}
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
						'label' => t("user.type"),
						'options' => $this->getTypesForSelect()
					));
					?>
					<div class="row">
						<div class="col-md-4">
							<?php
							$this->createField(array(
								'type' => 'select',
								'name' => 'country',
								'label' => t("user.country"),
								'options' => $this->getCountriesForSelect(),
								'ltr' => true
							));
							?>
						</div>
						<div class="col-md-4">
							<?php
							$this->createField(array(
								'name' => 'city',
								'label' => t("user.city")
							));
							?>
						</div>
						<div class="col-md-4">
							<?php
							$this->createField(array(
								'type' => 'number',
								'name' => 'zip',
								'label' => t("user.zip")
							));
							?>
						</div>
					</div>
					<?php
					$this->createField(array(
						'name' => 'address',
						'label' => t("user.address")
					));
					$this->createField(array( 
						'type' => 'radio',
						'name' => 'status',
						'label' => t("user.status"),
						'inline' => true,
						'options' => array(
							array(
								'label' => t("user.status.active"),
								'value' => user::active,
								'class' => 'grey'
							),
							array(
								'label' => t("user.status.suspend"),
								'value' => user::suspend,
								'class' => 'grey'
							),
							array(
								'label' => t("user.status.deactive"),
								'value' => user::deactive,
								'class' => 'grey'
							)
						)
					));
					if ($this->canChangeCredit) {
						$this->createField(array(
							'type' => 'number',
							'name' => 'credit',
							'label' => t("user.credit"),
							"ltr" => true,
						));
					} else {
						$this->createField(array(
								'type' => 'password',
								'name' => 'password',
								'label' => t("user.password"),
								'value' => ''
						));
					}
					$this->createField(array(
						'type' => 'password',
						'name' => 'password2',
						'label' => t("user.password_repeat"),
						'value' => ''
					));
					?>

				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<h3><?php echo t("userpanel.profile.socialnetworks"); ?></h3>
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
							'left' => true,
							'ltr' => true,
							'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.socialnetwork::telegram)
						),
						array(
							'name' => 'socialnets['.socialnetwork::instagram.']',
							'placeholder' => "Instagram",
							'icon' => 'fa fa-instagram',
							'left' => true,
							'ltr' => true,
							'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.socialnetwork::instagram)
						),
						array(
							'name' => 'socialnets['.socialnetwork::skype.']',
							'placeholder' => "Skype",
							'icon' => 'fa fa-skype',
							'left' => true,
							'ltr' => true,
							'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.socialnetwork::skype)
						)
					);
					foreach ($fields as $field) {
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
							'left' => true,
							'ltr' => true,
							'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.socialnetwork::twitter)
						),
						array(
							'name' => 'socialnets['.socialnetwork::facebook.']',
							'placeholder' => "Facebook",
							'icon' => 'clip-facebook',
							'left' => true,
							'ltr' => true,
							'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.socialnetwork::facebook)
						),
						array(
							'name' => 'socialnets['.socialnetwork::gplus.']',
							'placeholder' => "Google+",
							'icon' => 'fa fa-google-plus',
							'left' => true,
							'ltr' => true,
							'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.socialnetwork::gplus)
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
					<button class="btn btn-teal btn-block" type="submit"><i class="fa fa-arrow-circle-<?php echo ((bool)translator::getLang()->isRTL()) ? "left" : "right"; ?>"></i> <?php echo t("user.add"); ?></button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
$this->the_footer();
