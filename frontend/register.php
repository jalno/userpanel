<?php
use packages\base\Translator;
use packages\userpanel;

$isRTL = Translator::isRTL();
$this->the_header('login');
?>
<!-- start: REGISTER BOX -->
<div class="box-register" style="display: block;">
	<h3><?php echo t('register.title'); ?></h3>
	<p><?php echo t('register.enterdata'); ?></p>
	<form class="form-register" action="<?php echo userpanel\url('register'); ?>" method="post">
		<?php
        if ($errorcode = $this->getErrorsHTML()) {
            echo $errorcode;
        }
?>
		<div class="errorHandler alert alert-danger no-display">
			<i class="fa fa-remove-sign"></i> <?php echo t('register.error.recheck'); ?>
		</div>
		<fieldset>
			<div class="row">
				<?php foreach ($this->getFields() as $field) { ?>
				<div class="<?php echo $field['classes']; ?>">
					<?php $this->createField($field['field']); ?>
				</div>
				<?php } ?>
			</div>
			<?php
    $this->createField([
        'name' => 'password',
        'type' => 'password',
        'icon' => 'fa fa-lock',
        'placeholder' => t('register.user.password'),
    ]);
$this->createField([
    'name' => 'password_again',
    'type' => 'password',
    'icon' => 'fa fa-lock',
    'placeholder' => t('register.user.password_again'),
]);
if ($url = $this->getTOSUrl()) {
    $this->createField([
        'name' => 'tos',
        'type' => 'checkbox',
        'inline' => true,
        'options' => [
            [
                'value' => '1',
                'label' => t('register.accept_tos', ['url' => $url]),
            ],
        ],
    ]);
}
?>

			<div class="form-actions">
				<a class="btn btn-light-grey" href="<?php echo userpanel\url('login'); ?>"> <i class="fa fa-arrow-circle-<?php echo (!$isRTL) ? 'left' : 'right'; ?>"></i> <?php echo t('back'); ?></a>
				<button type="submit" class="btn btn-bricky pull-<?php echo ($isRTL) ? 'left' : 'right'; ?>"> <?php echo t('register.signup'); ?> <i class="fa fa-arrow-circle-<?php echo ($isRTL) ? 'left' : 'right'; ?>"></i></button>
			</div>
		</fieldset>
	</form>
</div>
<!-- end: REGISTER BOX -->
<?php $this->the_footer('login'); ?>
