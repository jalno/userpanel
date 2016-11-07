<?php
require_once("header.php");
use \packages\userpanel;
use \packages\userpanel\user;
use \packages\base\translator;
use \themes\clipone\utility;
?>
<!-- start: PAGE CONTENT -->
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-external-link-square"></i>لیست حساب ها
				<div class="panel-tools">
					<?php if($this->canAdd){ ?><a class="btn btn-xs btn-link" data-toggle="modal" href="#account-add"><i class="fa fa-plus tip tooltips" title="حساب جدید"></i></a><?php } ?>
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<?php
						$hasButtons = $this->hasButtons();
						?>
						<thead>
							<tr>
								<th class="center">#</th>
								<th><?php echo translator::trans("bank"); ?></th>
								<th><?php echo translator::trans("accnum"); ?></th>
								<th><?php echo translator::trans("cartnum"); ?></th>
								<th><?php echo translator::trans("master"); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach($this->getBankaccounts() as $account){
								$this->setButtonParam('edit', 'link', userpanel\url("settings/bankaccounts/edit/".$account->id));
								$this->setButtonParam('delete', 'link', userpanel\url("settings/bankaccounts/delete/".$account->id));
							?>
							<tr>
								<td class="center"><?php echo $account->id; ?></td>
								<td><?php echo $account->bank; ?></td>
								<td><?php echo $account->accnum; ?></td>
								<td><?php echo $account->cartnum; ?></td>
								<td><?php echo $account->master; ?></td>

								<?php
								if($hasButtons){
									echo("<td class=\"center\">".$this->genButtons()."</td>");
								}
								?>
								</tr>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- end: BASIC TABLE PANEL -->
	</div>
</div>
<!-- end: PAGE CONTENT-->
<?php if($this->canAdd){ ?>
<div class="modal fade" id="account-add" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('add')." ".translator::trans("accnum"); ?></h4>
	</div>
	<div class="modal-body">
		<form id="permission-add-form" action="<?php echo userpanel\url("settings/bankaccounts/add"); ?>" method="post" class="form-horizontal">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$this->createField(array(
				'label' => translator::trans("bank"),
				'name' => 'bank'
			));
			$this->createField(array(
				'label' => translator::trans("accnum"),
				'name' => 'accnum'
			));
			$this->createField(array(
				'label' => translator::trans("cartnum"),
				'name' => 'cartnum'
			));
			$this->createField(array(
				'label' => translator::trans("master"),
				'name' => 'master'
			));
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="permission-add-form" class="btn btn-success"><?php echo translator::trans("add") ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans("dissuasion") ?></button>
	</div>
</div>
<?php } ?>
<?php
require_once('footer.php');
