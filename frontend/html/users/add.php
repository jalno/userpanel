<?php
use packages\base\{Translator};
use packages\userpanel\User;
use packages\userpanel\User\SocialNetwork;

use function packages\userpanel\Url;

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
                        'name' => "visibility_{$field}",
                        'value' => true,
                    ]);
                }
            }
?>
			<h3><?php echo t('userpanel.profile.general_info'); ?></h3>
			<hr>
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
                'error' => [
                    'data_duplicate' => 'user.email.data_duplicate',
                ],
                'ltr' => true,
                'input-group' => $this->getInputGroupArrayFor('email'),
            ],
            [
                'name' => 'phone[number]',
                'label' => t('user.phone'),
                'ltr' => true,
                'input-group' => $this->getInputGroupArrayFor('phone'),
            ],
            [
                'name' => 'cellphone[number]',
                'label' => t('user.cellphone'),
                'error' => [
                    'data_duplicate' => 'user.cellphone.data_duplicate',
                ],
                'ltr' => true,
                'input-group' => $this->getInputGroupArrayFor('cellphone'),
            ],
        ];
if ($this->canChangeCredit) {
    $fields = array_merge($fields, [
        [
            'type' => 'password',
            'name' => 'password',
            'label' => t('user.password'),
            'value' => '',
        ],
    ]);
}
foreach ($fields as $field) {
    $this->createField($field);
}
?>
				</div>
				<div class="col-md-6">
					<?php

$this->createField([
    'type' => 'select',
    'name' => 'type',
    'label' => t('user.type'),
    'options' => $this->getTypesForSelect(),
]);
?>
					<div class="row">
						<div class="col-md-4">
							<?php
        $this->createField([
            'type' => 'select',
            'name' => 'country',
            'label' => t('user.country'),
            'options' => $this->getCountriesForSelect(),
            'ltr' => true,
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
]);
?>
						</div>
					</div>
					<?php
                    $this->createField([
                        'name' => 'address',
                        'label' => t('user.address'),
                    ]);
$this->createField([
    'type' => 'radio',
    'name' => 'status',
    'label' => t('user.status'),
    'inline' => true,
    'options' => [
        [
            'label' => t('user.status.active'),
            'value' => User::active,
            'class' => 'grey',
        ],
        [
            'label' => t('user.status.suspend'),
            'value' => User::suspend,
            'class' => 'grey',
        ],
        [
            'label' => t('user.status.deactive'),
            'value' => User::deactive,
            'class' => 'grey',
        ],
    ],
]);
if ($this->canChangeCredit) {
    $this->createField([
        'type' => 'number',
        'name' => 'credit',
        'label' => t('user.credit'),
        'ltr' => true,
    ]);
} else {
    $this->createField([
        'type' => 'password',
        'name' => 'password',
        'label' => t('user.password'),
        'value' => '',
    ]);
}
$this->createField([
    'type' => 'password',
    'name' => 'password2',
    'label' => t('user.password_repeat'),
    'value' => '',
]);
?>

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
					<button class="btn btn-teal btn-block" type="submit"><i class="fa fa-arrow-circle-<?php echo Translator::isRTL() ? 'left' : 'right'; ?>"></i> <?php echo t('user.add'); ?></button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
$this->the_footer();
