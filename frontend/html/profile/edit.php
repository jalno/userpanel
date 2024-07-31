<?php
use packages\base\Frontend\Theme;
use packages\base\Json;
use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\User\SocialNetwork;

$user = [
    'id' => $this->user->id,
    'name' => $this->user->name,
    'lastname' => $this->user->lastname,
    'cellphone' => $this->user->cellphone,
    'phone' => $this->user->phone,
];
?>
<form action="<?php echo userpanel\url('profile/edit'); ?>" method="POST" role="form" id="edit_form" data-user="<?php echo htmlentities(Json\Encode($user)); ?>">
	<?php
    if ($this->canEditPrivacy) {
        foreach ([
            'email',
            'cellphone',
            'phone',
            'socialnetworks_'.SocialNetwork::telegram,
            'socialnetworks_'.SocialNetwork::instagram,
            'socialnetworks_'.SocialNetwork::skype,
            'socialnetworks_'.SocialNetwork::twitter,
            'socialnetworks_'.SocialNetwork::facebook,
            'socialnetworks_'.SocialNetwork::gplus,
        ] as $field) {
            $this->createField([
                'type' => 'hidden',
                'name' => 'visibility_'.$field,
            ]);
        }
    }
?>
	<div class="row">
		<div class="col-md-12">
			<h3> <?php echo t('userpanel.profile.general_info'); ?> </h3>
			<hr>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<?php
        $fields = [
            [
                'name' => 'name',
                'label' => t('user.name'),
            ],
            [
                'name' => 'lastname',
                'label' => t('user.lastname'),
            ],
            [
                'type' => 'email',
                'name' => 'email',
                'label' => t('user.email'),
                'readonly' => true,
                'ltr' => true,
                'error' => [
                    'data_duplicate' => 'user.email.data_duplicate',
                ],
                'input-group' => $this->getInputGroupArrayFor('email'),
            ],
            [
                'name' => 'cellphone[number]',
                'label' => t('user.cellphone'),
                'readonly' => true,
                'ltr' => true,
                'error' => [
                    'data_duplicate' => 'user.cellphone.data_duplicate',
                ],
                'input-group' => $this->getInputGroupArrayFor('cellphone'),
            ],
            [
                'type' => 'password',
                'name' => 'password',
                'label' => t('user.password'),
                'value' => '',
            ],
            [
                'type' => 'password',
                'name' => 'password2',
                'label' => t('user.password_repeat'),
                'value' => '',
            ],
        ];
foreach ($fields as $field) {
    $this->createField($field);
}
?>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-4">
					<?php
        $this->createField([
            'type' => 'select',
            'name' => 'country',
            'label' => t('user.country'),
            'options' => $this->getCountriesForSelect(),
        ]);
?>
				</div>
				<div class="col-md-4">
					<?php
$this->createField([
    'name' => 'city',
    'label' => t('user.city'),
]);
?>
				</div>
				<div class="col-md-4">
					<?php
$this->createField([
    'type' => 'number',
    'name' => 'zip',
    'label' => t('user.zip'),
    'ltr' => true,
]);
?>
				</div>
			</div>
			<?php
            $fields = [
                [
                    'name' => 'address',
                    'label' => t('user.address'),
                ],
                [
                    'name' => 'phone[number]',
                    'label' => t('user.phone'),
                    'ltr' => true,
                    'input-group' => $this->getInputGroupArrayFor('phone'),
                ],
            ];
foreach ($fields as $field) {
    $this->createField($field);
}
?>
			<div class="form-group">
				<label><?php echo t('user.avatar'); ?></label>
				<div class="user-image avatarPreview" style="width: 162px;">
					<img src="<?php echo $this->getAvatarURL(); ?>" class="preview img-responsive">
					<input name="avatar" type="file">
					<div class="button-group">
						<button type="button" class="btn btn-teal btn-sm btn-upload"><i class="fa fa-pencil"></i></button>
						<button type="button" class="btn btn-bricky btn-sm btn-remove" data-default="<?php echo Theme::url('assets/images/defaultavatar.jpg'); ?>"><i class="fa fa-times"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h3><?php echo t('userpanel.profile.socialnetworks'); ?></h3>
			<hr>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<?php
$fields = [
    [
        'name' => 'socialnets['.SocialNetwork::telegram.']',
        'placeholder' => 'Telegram',
        'icon' => 'fa fa-telegram',
        'left' => true,
        'ltr' => true,
        'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.SocialNetwork::telegram),
    ],
    [
        'name' => 'socialnets['.SocialNetwork::instagram.']',
        'placeholder' => 'Instagram',
        'icon' => 'fa fa-instagram',
        'left' => true,
        'ltr' => true,
        'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.SocialNetwork::instagram),
    ],
    [
        'name' => 'socialnets['.SocialNetwork::skype.']',
        'placeholder' => 'Skype',
        'icon' => 'fa fa-skype',
        'left' => true,
        'ltr' => true,
        'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.SocialNetwork::skype),
    ],
];
foreach ($fields as $field) {
    $this->createField($field);
}
?>
		</div>
		<div class="col-md-6">
			<?php
$fields = [
    [
        'name' => 'socialnets['.SocialNetwork::twitter.']',
        'placeholder' => 'Twitter',
        'icon' => 'clip-twitter',
        'left' => true,
        'ltr' => true,
        'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.SocialNetwork::twitter),
    ],
    [
        'name' => 'socialnets['.SocialNetwork::facebook.']',
        'placeholder' => 'Facebook',
        'icon' => 'clip-facebook',
        'left' => true,
        'ltr' => true,
        'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.SocialNetwork::facebook),
    ],
    [
        'name' => 'socialnets['.SocialNetwork::gplus.']',
        'placeholder' => 'Google+',
        'icon' => 'fa fa-google-plus',
        'left' => true,
        'ltr' => true,
        'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.SocialNetwork::gplus),
    ],
];
foreach ($fields as $field) {
    $this->createField($field);
}
?>
		</div>
	</div>
	<div class="row" style="margin-top: 20px;margin-bottom: 20px;">
		<div class="col-md-offset-4 col-md-4">
			<button class="btn btn-success btn-block" type="submit"><i class="fa fa-check-square-o"></i> <?php echo t('user.profile.save'); ?></button>
		</div>
	</div>
</form>
