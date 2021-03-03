<?php
use \packages\base;
use \packages\base\frontend\theme;
use \packages\base\translator;
use \packages\userpanel;
$isRTL = (bool) base\translator::getLang()->isRTL();
$this->the_header('login');
?>
<!-- start: REGISTER BOX -->
<div class="box-register" style="display: block;">
	<h3><?php echo translator::trans('register.title'); ?></h3>
	<p><?php echo translator::trans('register.enterdata'); ?></p>
	<form class="form-register" action="<?php echo userpanel\url('register'); ?>" method="post">
		<?php
		if ($errorcode = $this->getErrorsHTML()) {
			echo $errorcode;
		}
		?>
		<div class="errorHandler alert alert-danger no-display">
			<i class="fa fa-remove-sign"></i> <?php echo translator::trans('register.error.recheck'); ?>
		</div>
		<fieldset>
			<div class="row">
				<div class="col-md-6">
					<?php
					$this->createField(array(
						'name' => 'name',
						'placeholder' => translator::trans('register.user.name')
					));
					?>
				</div>
				<div class="col-md-6">
					<?php
					$this->createField(array(
						'name' => 'lastname',
						'placeholder' => translator::trans('register.user.lastname')
					));
					?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<?php
					$this->createField(array(
						'type' => 'select',
						'name' => 'country',
						'placeholder' => translator::trans('register.user.country'),
						'options' => $this->getCountriesForSelect(),
					));
					?>
				</div>
				<div class="col-md-6">
					<?php
					$this->createField(array(
						'name' => 'city',
						'placeholder' => translator::trans('register.user.city')
					));
					?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<?php
					$this->createField(array(
						'name' => 'address',
						'placeholder' => translator::trans('register.user.address')
					));
					?>
				</div>
				<div class="col-md-6">
					<?php
					$this->createField(array(
						'name' => 'zip',
						'placeholder' => translator::trans('register.user.zip')
					));
					?>
				</div>
			</div>
			<?php
			$this->createField(array(
				'name' => 'phone[number]',
				'placeholder' => translator::trans('register.user.phone'),
				'input-group' => array(
					'first' => array(
						array(
							'type' => 'select',
							'name' => 'phone[code]',
							'options' => array(),
						),
					),
				),
			));
			$this->createField(array(
				'name' => 'cellphone[number]',
				'placeholder' => translator::trans('register.user.cellphone'),
				'input-group' => array(
					'first' => array(
						array(
							'type' => 'select',
							'name' => 'cellphone[code]',
							'options' => array(),
						),
					),
				),
			));
			$this->createField(array(
				'name' => 'email',
				'type' => 'email',
				'icon' => 'fa fa-envelope',
				'placeholder' => translator::trans('register.user.email')
			));
			$this->createField(array(
				'name' => 'password',
				'type' => 'password',
				'icon' => 'fa fa-lock',
				'placeholder' => translator::trans('register.user.password')
			));
			$this->createField(array(
				'name' => 'password_again',
				'type' => 'password',
				'icon' => 'fa fa-lock',
				'placeholder' => translator::trans('register.user.password_again')
			));
			if ($url = $this->getTOSUrl()) {
				$this->createField(array(
					'name' => 'tos',
					'type' => 'checkbox',
					'inline' => true,
					'options' => array(
						array(
							'value' => '1',
							'label' => t('register.accept_tos', array("url" => $url)),
						)
					)
				));
			}
			?>

			<div class="form-actions">
				<a class="btn btn-light-grey" href="<?php echo userpanel\url('login'); ?>"> <i class="fa fa-arrow-circle-<?php echo (!$isRTL) ? "left" : "right"; ?>"></i> <?php echo translator::trans('back'); ?></a>
				<button type="submit" class="btn btn-bricky pull-<?php echo ($isRTL) ? "left" : "right"; ?>"> <?php echo translator::trans('register.signup'); ?> <i class="fa fa-arrow-circle-<?php echo ($isRTL) ? "left" : "right"; ?>"></i></button>
			</div>
		</fieldset>
	</form>
</div>
<!-- end: REGISTER BOX -->
<?php $this->the_footer('login'); ?>
