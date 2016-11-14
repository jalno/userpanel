<?php
require_once("header.php");
use \packages\userpanel;
use \packages\userpanel\user;
use \packages\base\translator;
use \themes\clipone\utility;
$account = $this->getBankaccount();
?>
<form action="<?php echo(userpanel\url("settings/bankaccounts/edit/".$account->id)); ?>" method="post">
	<div class="row">
		<div class="col-md-6">
			<?php
			$this->createField(array(
				'name' => 'bank',
				'label' => translator::trans('bank'),
				'value' => $account->bank
			));
			$this->createField(array(
				'name' => 'accnum',
				'label' => translator::trans('accnum'),
				'value' => $account->accnum
			));
			?>
		</div>
		<div class="col-md-6">
			<?php
			$this->createField(array(
				'name' => 'cartnum',
				'label' => translator::trans('cartnum'),
				'value' => $account->cartnum
			));
			$this->createField(array(
				'name' => 'master',
				'label' => translator::trans('master'),
				'value' => $account->master
			));
			?>
		</div>
	</div>
	<!-- end: CONDENSED TABLE PANEL -->
	<div class="row" style="margin-top: 20px;margin-bottom: 20px;">
		<div class="col-md-offset-4 col-md-4">
			<button class="btn btn-teal btn-block" type="submit"><i class="fa fa-arrow-circle-left"></i> <?php echo translator::trans('bankaccount.edit'); ?></button>
		</div>
	</div>
</form>
<?php
require_once('footer.php');
