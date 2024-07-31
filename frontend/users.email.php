<?php
require_once 'header.php';
use packages\base\Translator;
use packages\userpanel;

?>
<!-- start: PAGE CONTENT -->
<div class="row">
	<div class="col-sm-12">
		<div class="tabbable">
			<ul class="nav nav-tabs tab-padding tab-space-3 tab-blue">
				<li><a href="<?php echo userpanel\url('users/email/'.$this->getDataForm('id')); ?>"><?php echo t('user.profile.overview'); ?></a></li>
				<li class="active"><a data-toggle="tab" href="#edit_panel">ویرایش اطلاعات</a></li>
			</ul>
			<div class="tab-content">
				<div id="edit_panel" class="tab-pane active">
					<form action="<?php echo userpanel\url('users/edit/'.$this->getDataForm('id')); ?>" method="POST" role="form" id="eedit_form">
						<input type="hidden" name="user" value="<?php echo $this->getDataForm('id'); ?>" />
						<div class="row">
							<div class="col-md-12">
								<h3>اطلاعات پایه</h3>
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
                                        'type' => 'email',
                                        'name' => 'email',
                                        'label' => t('user.email'),
                                        'error' => [
                                            'data_duplicate' => 'user.email.data_duplicate',
                                        ],
                                    ],
                                    [
                                        'name' => 'phone',
                                        'label' => t('user.phone'),
                                    ],
                                    [
                                        'name' => 'cellphone',
                                        'label' => t('user.cellphone'),
                                        'error' => [
                                            'data_duplicate' => 'user.cellphone.data_duplicate',
                                        ],
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
								<?php

$this->createField([
    'type' => 'select',
    'name' => 'type',
    'label' => t('user.type'),
    'options' => $this->usertypes,
]);
?>
								<div class="row">
									<div class="col-md-4">
										<?php
        $this->createField([
            'type' => 'number',
            'name' => 'zip',
            'label' => t('user.zip'),
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
    'name' => 'country',
    'label' => t('user.country'),
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
            'value' => 1,
            'class' => 'grey',
        ],
        [
            'label' => t('user.status.suspend'),
            'value' => 0,
            'class' => 'grey',
        ],
        [
            'label' => t('user.status.deactive'),
            'value' => 0,
            'class' => 'grey',
        ],
    ],
]);
$this->createField([
    'type' => 'number',
    'name' => 'credit',
    'label' => t('user.credit'),
]);
?>

							</div>
						</div>
						<div class="row" style="margin-top: 20px;margin-bottom: 20px;">
							<div class="col-md-offset-4 col-md-4">
								<button class="btn btn-teal btn-block" type="submit"><i class="fa fa-arrow-circle-left"></i> <?php echo t('user.profile.save'); ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
require_once 'footer.php';
