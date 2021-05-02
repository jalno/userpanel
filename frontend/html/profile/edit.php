<?php
use packages\base\{json, frontend\theme};
use packages\base\translator;
use packages\userpanel;
use packages\userpanel\user\socialnetwork;

$user = array(
	"id" => $this->user->id,
	"name" => $this->user->name,
	"lastname" => $this->user->lastname,
	"cellphone" => $this->user->cellphone,
	"phone" => $this->user->phone,
);
?>
<form action="<?php echo userpanel\url('profile/edit'); ?>" method="POST" role="form" id="edit_form" data-user="<?php echo htmlentities(json\encode($user)); ?>">
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
			<h3> <?php echo t("userpanel.profile.general_info"); ?> </h3>
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
					'readonly' => true,
					'ltr' => true,
					'error' => array(
						'data_duplicate' => 'user.email.data_duplicate'
					),
					'input-group' => $this->getInputGroupArrayFor('email')
				),
				array(
					'name' => 'cellphone[number]',
					'label' => translator::trans("user.cellphone"),
					'readonly' => true,
					'ltr' => true,
					'error' => array(
						'data_duplicate' => 'user.cellphone.data_duplicate'
					),
					'input-group' => $this->getInputGroupArrayFor('cellphone')
				),
				array(
					'type' => 'password',
					'name' => 'password',
					'label' => translator::trans("user.password"),
					'value' => ''
				),
				array(
					'type' => 'password',
					'name' => 'password2',
					'label' => translator::trans("user.password_repeat"),
					'value' => ''
				)
			);
			foreach($fields as $field){
				$this->createField($field);
			}
			?>
		</div>
		<div class="col-md-6">
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
						'label' => translator::trans("user.zip"),
						'ltr' => true
					));
					?>
				</div>
			</div>
			<?php
			$fields = array(
				array(
					'name' => 'address',
					'label' => translator::trans("user.address")
				),
				array(
					'name' => 'phone[number]',
					'label' => translator::trans("user.phone"),
					'ltr' => true,
					'input-group' => $this->getInputGroupArrayFor('phone')
				)
			);
			foreach($fields as $field){
				$this->createField($field);
			}
			?>
			<div class="form-group">
				<label><?php echo t("user.avatar"); ?></label>
				<div class="user-image avatarPreview" style="width: 162px;">
					<img src="<?php echo $this->getAvatarURL(); ?>" class="preview img-responsive">
					<input name="avatar" type="file">
					<div class="button-group">
						<button type="button" class="btn btn-teal btn-sm btn-upload"><i class="fa fa-pencil"></i></button>
						<button type="button" class="btn btn-bricky btn-sm btn-remove" data-default="<?php echo theme::url('assets/images/defaultavatar.jpg'); ?>"><i class="fa fa-times"></i></button>
					</div>
				</div>
			</div>
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
			<button class="btn btn-success btn-block" type="submit"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("user.profile.save"); ?></button>
		</div>
	</div>
</form>
