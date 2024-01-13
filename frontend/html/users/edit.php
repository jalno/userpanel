<?php
use packages\base\{Json, translator};
use packages\userpanel;
use packages\userpanel\{user, user\socialnetwork};

$user = array(
	"id" => $this->user->id,
	"name" => $this->user->name,
	"lastname" => $this->user->lastname,
	"type" => $this->user->type->id,
	"cellphone" => $this->user->cellphone,
	"phone" => $this->user->phone,
	"has_custom_permissions" => $this->user->has_custom_permissions,
	"status" => $this->user->status,
	"options" => array(),
);
?>
<form action="<?php echo userpanel\url('users/edit/'.$this->user->id); ?>" method="POST" role="form" id="edit_form" data-can-edit-permissions="<?php echo Json\encode($this->canEditPermissions); ?>" data-user="<?php echo htmlentities(json\encode($user)); ?>">
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
				'name' => "visibility_".$field
			));

		}
	}
	?>
	<div class="row">
		<div class="col-md-12">
			<h3><?php echo t("userpanel.profile.general_info"); ?></h3>
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
					'input-group' => $this->getInputGroupArrayFor('email')
				),
				array(
					'name' => 'phone[number]',
					'label' => translator::trans("user.phone"),
					'ltr' => true,
					'input-group' => $this->getInputGroupArrayFor('phone')
				),
				array(
					'name' => 'cellphone[number]',
					'label' => translator::trans("user.cellphone"),
					'ltr' => true,
					'error' => array(
						'data_duplicate' => 'user.cellphone.data_duplicate'
					),
					'input-group' => $this->getInputGroupArrayFor('cellphone')
				),
			);
			if ($this->canChangeCredit and $this->canEditPassword) {
				$fields = array_merge($fields, array(
					array(
						'type' => 'password',
						'name' => 'password',
						'label' => translator::trans("user.password"),
						'value' => ''
					),
				));
			}
			foreach ($fields as $field) {
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
						'options' => $this->getCountriesForSelect()
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
			if ($this->canChangeCredit) {
				$this->createField(array(
					'type' => 'number',
					'name' => 'credit',
					'label' => translator::trans("user.credit"),
					"ltr" => true,
					"input-group" => array(
						"right" => array(
							array(
								"type" => "addon",
								"text" => $this->getUserCurrency(),
							),
						),
					),
				));
			} else if ($this->canEditPassword) {
				$this->createField(array(
						'type' => 'password',
						'name' => 'password',
						'label' => translator::trans("user.password"),
						'value' => ''
				));
			}
			if ($this->canEditPassword) {
				$this->createField(array(
					'type' => 'password',
					'name' => 'password2',
					'label' => translator::trans("user.password_repeat"),
					'value' => ''
				));
			}
			?>

		</div>
	</div>

	<?php
		$socialnetworksFirstPart = array(
			array(
				'name' => 'socialnets['.socialnetwork::telegram.']',
				'placeholder' => 'Telegram',
				'icon' => 'fa fa-telegram',
				'left' => true,
				'ltr' => true,
				'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.socialnetwork::telegram),
			),
			array(
				'name' => 'socialnets['.socialnetwork::instagram.']',
				'placeholder' => 'Instagram',
				'icon' => 'fa fa-instagram',
				'left' => true,
				'ltr' => true,
				'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.socialnetwork::instagram),
			),
			array(
				'name' => 'socialnets['.socialnetwork::skype.']',
				'placeholder' => 'Skype',
				'icon' => 'fa fa-skype',
				'left' => true,
				'ltr' => true,
				'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.socialnetwork::skype),
			),
		);
		if (!$this->canEditPermissions) {
	?>
	<h3><?php echo t("userpanel.profile.socialnetworks"); ?></h3>
		<hr>
	<?php } ?>

	<div class="row">
		<div class="col-md-6">
		<?php
		if ($this->canEditPermissions) {
		?>
			<div class="change-permissions-container">
				<h3>
					<?php echo t("userpanel.profile.user_permissions"); ?>
					<?php if ($this->user->has_custom_permissions) { ?>
						<i class="fa fa-exclamation-circle warning-custom-permissions tooltips" title="<?php echo t("userpanel.users.edit.usertype.custom_permissions.warn_text"); ?>"></i>
					<?php } ?>
					</h3>
				<hr>
				<div class="userpanel-permissions-fancytree-container"></div>
			</div>
		<?php
		} else {
			foreach ($socialnetworksFirstPart as $field) {
				$this->createField($field);
			}
		}
		?>
		</div>
		<div class="col-md-6">
		<?php if ($this->canEditPermissions) { ?>
			<h3><?php echo t("userpanel.profile.socialnetworks"); ?></h3>
			<hr>
		<?php
		}
		$fields = array_merge(($this->canEditPermissions ? $socialnetworksFirstPart : []), array(
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
				'placeholder' => " Google+",
				'icon' => 'fa fa-google-plus',
				'left' => true,
				'ltr' => true,
				'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.socialnetwork::gplus)
			),
		));
		foreach ($fields as $field) {
			$this->createField($field);
		}
		?>
		</div>
	</div>
	<div class="row" style="margin-top: 20px;margin-bottom: 20px;">
		<div class="col-md-offset-4 col-md-4">
			<button class="btn btn-teal btn-block" type="submit">
				<i class="fa fa-check-square-o"></i> <?php echo t("user.profile.save"); ?>
			</button>
		</div>
	</div>
</form>
