<?php
use packages\base\Json;
use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\User;
use packages\userpanel\User\SocialNetwork;

$user = [
    'id' => $this->user->id,
    'name' => $this->user->name,
    'lastname' => $this->user->lastname,
    'type' => $this->user->type->id,
    'cellphone' => $this->user->cellphone,
    'phone' => $this->user->phone,
    'has_custom_permissions' => $this->user->has_custom_permissions,
    'status' => $this->user->status,
    'options' => [],
];
?>
<form action="<?php echo userpanel\url('users/edit/'.$this->user->id); ?>" method="POST" role="form" id="edit_form" data-can-edit-permissions="<?php echo Json\encode($this->canEditPermissions); ?>" data-user="<?php echo htmlentities(json\encode($user)); ?>">
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
			<h3><?php echo t('userpanel.profile.general_info'); ?></h3>
			<hr>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<?php
        $fields = [
            [
                'name' => 'name',
                'label' => Translator::trans('user.name'),
            ],
            [
                'name' => 'lastname',
                'label' => Translator::trans('user.lastname'),
            ],
            [
                'type' => 'email',
                'name' => 'email',
                'label' => Translator::trans('user.email'),
                'error' => [
                    'data_duplicate' => 'user.email.data_duplicate',
                ],
                'ltr' => true,
                'input-group' => $this->getInputGroupArrayFor('email'),
            ],
            [
                'name' => 'phone[number]',
                'label' => Translator::trans('user.phone'),
                'ltr' => true,
                'input-group' => $this->getInputGroupArrayFor('phone'),
            ],
            [
                'name' => 'cellphone[number]',
                'label' => Translator::trans('user.cellphone'),
                'ltr' => true,
                'error' => [
                    'data_duplicate' => 'user.cellphone.data_duplicate',
                ],
                'input-group' => $this->getInputGroupArrayFor('cellphone'),
            ],
        ];
if ($this->canChangeCredit and $this->canEditPassword) {
    $fields = array_merge($fields, [
        [
            'type' => 'password',
            'name' => 'password',
            'label' => Translator::trans('user.password'),
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
    'label' => Translator::trans('user.type'),
    'options' => $this->getTypesForSelect(),
]);
?>
			<div class="row">
				<div class="col-md-4">
					<?php
        $this->createField([
            'type' => 'select',
            'name' => 'country',
            'label' => Translator::trans('user.country'),
            'options' => $this->getCountriesForSelect(),
        ]);
?>
				</div>
				<div class="col-md-4">
					<?php
$this->createField([
    'name' => 'city',
    'label' => Translator::trans('user.city'),
]);
?>
				</div>
				<div class="col-md-4">
					<?php
$this->createField([
    'type' => 'number',
    'name' => 'zip',
    'label' => Translator::trans('user.zip'),
]);
?>
				</div>
			</div>
			<?php
            $this->createField([
                'name' => 'address',
                'label' => Translator::trans('user.address'),
            ]);
$this->createField([
    'type' => 'radio',
    'name' => 'status',
    'label' => Translator::trans('user.status'),
    'inline' => true,
    'options' => [
        [
            'label' => Translator::trans('user.status.active'),
            'value' => User::active,
            'class' => 'grey',
        ],
        [
            'label' => Translator::trans('user.status.suspend'),
            'value' => User::suspend,
            'class' => 'grey',
        ],
        [
            'label' => Translator::trans('user.status.deactive'),
            'value' => User::deactive,
            'class' => 'grey',
        ],
    ],
]);
if ($this->canChangeCredit) {
    $this->createField([
        'type' => 'number',
        'name' => 'credit',
        'label' => Translator::trans('user.credit'),
        'ltr' => true,
        'input-group' => [
            'right' => [
                [
                    'type' => 'addon',
                    'text' => $this->getUserCurrency(),
                ],
            ],
        ],
    ]);
} elseif ($this->canEditPassword) {
    $this->createField([
        'type' => 'password',
        'name' => 'password',
        'label' => Translator::trans('user.password'),
        'value' => '',
    ]);
}
if ($this->canEditPassword) {
    $this->createField([
        'type' => 'password',
        'name' => 'password2',
        'label' => Translator::trans('user.password_repeat'),
        'value' => '',
    ]);
}
?>

		</div>
	</div>

	<?php
        $socialnetworksFirstPart = [
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
if (!$this->canEditPermissions) {
    ?>
	<h3><?php echo t('userpanel.profile.socialnetworks'); ?></h3>
		<hr>
	<?php } ?>

	<div class="row">
		<div class="col-md-6">
		<?php
        if ($this->canEditPermissions) {
            ?>
			<div class="change-permissions-container">
				<h3>
					<?php echo t('userpanel.profile.user_permissions'); ?>
					<?php if ($this->user->has_custom_permissions) { ?>
						<i class="fa fa-exclamation-circle warning-custom-permissions tooltips" title="<?php echo t('userpanel.users.edit.usertype.custom_permissions.warn_text'); ?>"></i>
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
			<h3><?php echo t('userpanel.profile.socialnetworks'); ?></h3>
			<hr>
		<?php
		}
$fields = array_merge($this->canEditPermissions ? $socialnetworksFirstPart : [], [
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
        'placeholder' => ' Google+',
        'icon' => 'fa fa-google-plus',
        'left' => true,
        'ltr' => true,
        'input-group' => $this->getInputGroupArrayFor('socialnetworks_'.SocialNetwork::gplus),
    ],
]);
foreach ($fields as $field) {
    $this->createField($field);
}
?>
		</div>
	</div>
	<div class="row" style="margin-top: 20px;margin-bottom: 20px;">
		<div class="col-md-offset-4 col-md-4">
			<button class="btn btn-teal btn-block" type="submit">
				<i class="fa fa-check-square-o"></i> <?php echo t('user.profile.save'); ?>
			</button>
		</div>
	</div>
</form>
