<?php
use packages\base\Translator;

use function packages\userpanel\url;

$isRTL = Translator::getLang()->isRTL();
$this->the_header('login');
?>
<div class="box-forgot">
	<div class="errors">
		<?php
        $errorcode = $this->getErrorsHTML();
if ($errorcode) {
    ?>
		<div class="row">
			<div class="col-xs-12"><?php echo $errorcode; ?></div>
		</div>
		<?php } ?>
	</div>
	<form action="<?php echo url('resetpwd'); ?>" class="form-resetpwd" method="POST">
		<h3><?php echo t('resetpwd.box.title'); ?></h3>
		<p>
		‍	<?php echo t('resetpwd.box.description'); ?>
		</p>
		<div class="errorHandler alert alert-danger no-display">
			<i class="fa fa-remove-sign"></i> <?php echo t('userpanel.data_validation.resetpwd.emailorcellphone'); ?>
		</div>
		<fieldset>
			<?php $this->createField([
			    'name' => 'method',
			    'type' => 'radio',
			    'inline' => true,
			    'options' => $this->getChannelsOptions(),
			]); ?>
			<div class="input-group floating-label-group credential-container">
			<?php if (!$isRTL) { ?>
				<span class="input-group-btn form-group hidden">
					<select name="credential[code]" class="form-control"></select>
				</span>
			<?php } ?>
				<span class="input-icon input-icon-right">
					<input type="text" name="credential" class="form-control ltr" required>
					<span class="floating-label"><?php echo t('userpanel.login.email_or_phone'); ?></span>
					<i class="fa fa-user"></i>
				</span>
			<?php if ($isRTL) { ?>
				<span class="input-group-btn form-group hidden">
					<select name="credential[code]" class="form-control"></select>
				</span>
			<?php } ?>
			</div>

			<div class="form-actions">
				<a class="btn btn-light-grey pull-<?php echo (!$isRTL) ? 'left' : 'right'; ?>" href="<?php echo url('login'); ?>">
					<i class="fa fa-arrow-circle-<?php echo (!$isRTL) ? 'left' : 'right'; ?>"></i> <?php echo t('resetpwd.return'); ?>
				</a>
				<button type="submit" class="btn btn-bricky pull-<?php echo ($isRTL) ? 'left' : 'right'; ?>" <?php if ($this->hasBlocked()) {
				    echo 'disabled';
				} ?>>
					<i class="fa fa-unlock-alt"></i> <?php echo t('resetpwd.recovery'); ?>
				</button>
			</div>
		</fieldset>
	</form>
	<form action="<?php echo url('resetpwd/token'); ?>" class="form-authentication" method="POST">
		<h3><?php echo t('resetpwd.token.sent.title'); ?></h3>
		<p><?php echo t('resetpwd.token.sent.description'); ?></p>
		<p class="text-center">
			<a class="btn-goback" href="<?php echo url('resetpwd'); ?>"><?php echo t('resetpwd.token.retry'); ?></a>
		</p>
		<fieldset>
			<?php
            $this->createField([
				'name' => 'credential',
				'type' => 'hidden',
            ]);
$this->createField([
    'name' => 'token',
    'icon' => 'fa fa-key',
    'left' => true,
    'placeholder' => t('resetpwd.token'),
    'type' => 'number',
]);
?>
			<div class="form-actions">
				<a class="btn btn-light-grey btn-goback" href="<?php echo url('resetpwd'); ?>"> <i class="fa fa-arrow-circle-<?php echo (!$isRTL) ? 'left' : 'right'; ?>"></i> <?php echo t('back'); ?></a>
				<button type="submit" class="btn btn-success pull-left">
					<i class="fa fa-check-square-o "></i> <?php echo t('resetpwd.confirm'); ?>
				</button>
			</div>
		</fieldset>
	</form>
	<div class="alert alert-success no-display email-alert" role="alert">
		<strong><?php echo t('userpanel.resetpwd.withemail.success.title'); ?></strong>
		<p><?php echo t('userpanel.resetpwd.withemail.success.description'); ?></p>
	</div>
</div>
<?php
$this->the_footer('login');
