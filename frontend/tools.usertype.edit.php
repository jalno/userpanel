<?php
require_once("header.php");
use \packages\userpanel;
use \packages\userpanel\user;
use \packages\base\translator;
use \themes\clipone\utility;
$usertype = $this->getUserType();
$hasButtons = $this->hasButtons();
?>
<form action="<?php echo(userpanel\url("tools/usertype/edit/".$usertype->id)); ?>" method="post">
	<div class="row">
		<div class="col-md-6">
			<!-- start: BASIC TABLE PANEL -->
			<?php
			$this->createField(array(
				'name' => 'title',
				'label' => translator::trans('usertype.title'),
				'value' => $usertype->title
			));
			?>
			<!-- end: BASIC TABLE PANEL -->
		</div>
	</div>
	<div class="row">
		<!-- start: CONDENSED TABLE PANEL -->
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-external-link-square"></i> Condensed table
					<div class="panel-tools">
						<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('permission.add'); ?>" data-toggle="modal" href="#permissionAdd" data-original-title=""><i class="fa fa-plus"></i></a>
						<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
					</div>
				</div>
				<div class="panel-body">
					<table class="table table-condensed table-hover" id="sample-table-3">
						<thead>
							<tr>
								<th></th>
								<th>#</th>
								<th class="hidden-xs"><?php echo translator::trans('permission.type'); ?></th>
								<th class="hidden-xs"><?php echo translator::trans('permission.title'); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							$number = 1;
							foreach($usertype->permissions as $permission){
								$this->setButtonParam('permissionedit', 'link', "#permissionEdit");
								?>
								<tr data-permission="<?php echo($permission->name); ?>">
									<td class="center hidden-xs">
										<div class="checkbox-table">
											<label>
												<div class="icheckbox_flat-grey" style="position: relative;">
													<?php $this->createField(array(
														'name' => "permission({$number})",
														'type' => 'checkbox',
														'label' => false,
														'options' => array(
															array(
																'value' => $permission->name,
																'class' => 'flat-grey'
															)
														),
														'value' => $permission->name
													)); ?>
												</div>
											</label>
										</div>
									</td>
									<td><?php echo $number; ?></td>
									<td><?php echo $permission->type->title; ?></td>
									<td><?php echo $permission->name; ?></td>
									<?php
									if($hasButtons){
										echo("<td class=\"center\">".$this->genButtons()."</td>");
									}
									?>
								</tr>
								<?php
								$number++;
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- end: CONDENSED TABLE PANEL -->
		<!-- start: CONDENSED TABLE PANEL -->
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-external-link-square"></i> Condensed table
					<div class="panel-tools">
						<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('priority.add'); ?>" data-toggle="modal" href="#priorityadd" data-original-title=""><i class="fa fa-plus"></i></a>
						<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
					</div>
				</div>
				<div class="panel-body">
					<table class="table table-condensed table-hover" id="sample-table-3">
						<thead>
							<tr>
								<th></th>
								<th>#</th>
								<th class="hidden-xs"><?php echo translator::trans('usertype.parent'); ?></th>
								<th class="hidden-xs"><?php echo translator::trans('usertype.child'); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							$number = 1;
							foreach($usertype->children as $priority){
								$this->setButtonParam('priorityedit', 'link', "#priorityEdit");
							?>
								<tr data-priority="<?php echo($priority->childdata['id']); ?>">
									<td class="center hidden-xs">
										<div class="checkbox-table">
											<label>
												<div class="icheckbox_flat-grey" style="position: relative;">
													<?php $this->createField(array(
														'name' => "priority({$number})",
														'type' => 'checkbox',
														'label' => false,
														'options' => array(
															array(
																'value' => $priority->childdata['id'],
																'class' => 'flat-grey'
															)
														),
														'value' => $priority->childdata['id']
													)); ?>
												</div>
											</label>
										</div>
									</td>
									<td><?php echo $number; ?></td>
									<td><?php echo $priority->parentdata['title']; ?></td>
									<td><?php echo $priority->childdata['title']; ?></td>
									<?php
									if($hasButtons){
										echo("<td class=\"center\">".$this->genButtons()."</td>");
									}
									?>
								</tr>
								<?php
								$number++;
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- end: CONDENSED TABLE PANEL -->
		<div class="row" style="margin-top: 20px;margin-bottom: 20px;">
			<div class="col-md-offset-4 col-md-4">
				<button class="btn btn-teal btn-block" type="submit"><i class="fa fa-arrow-circle-left"></i> بروزرسانی</button>
			</div>
		</div>
	</div>
</form>
<div class="modal fade" id="priorityEdit" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('usertype.add'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="priorityEditForm" action="" method="post" class="form-horizontal">
			<input type="hidden" name="parent" value="<?php echo $usertype->id; ?>">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$this->createField(array(
				'type' => 'select',
				'name' => 'child',
				'label' => translator::trans("usertype.child"),
				'id' => 'priorityedit',
				'options' => $this->getPerioritySelctbox()
			));
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="priorityEditForm" class="btn btn-success"><?php echo translator::trans("change"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans("dissuasion") ?></button>
	</div>
</div>
<div class="modal fade" id="permissionEdit" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('permission.edit'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="permissionEditForm" action="" method="post" class="form-horizontal">
			<input type="hidden" name="type" value="<?php echo $usertype->id; ?>">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$this->createField(array(
				'name' => 'name',
				'label' => translator::trans('permission.title'),
				'class' => 'form-control ltr'
			));
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="permissionEditForm" class="btn btn-success"><?php echo translator::trans("change"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans("dissuasion") ?></button>
	</div>
</div>
<div class="modal fade" id="priorityadd" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('usertype.add'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="priorityaddForm" action="<?php echo userpanel\url("tools/children/add"); ?>" method="post" class="form-horizontal">
			<input type="hidden" name="parent" value="<?php echo $usertype->id; ?>">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$this->createField(array(
				'type' => 'select',
				'name' => 'child',
				'label' => translator::trans("usertype.child"),
				'id' => 'priorityedit',
				'options' => $this->getPerioritySelctbox()
			));
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="priorityaddForm" class="btn btn-success"><?php echo translator::trans("add"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans("dissuasion") ?></button>
	</div>
</div>
<div class="modal fade" id="permissionAdd" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('permission.add'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="permissionAddForm" action="<?php echo userpanel\url("tools/permissions/add"); ?>" method="post" class="form-horizontal">
			<input type="hidden" name="type" value="<?php echo $usertype->id; ?>">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$this->createField(array(
				'name' => 'name',
				'label' => translator::trans('permission.title'),
				'class' => 'form-control ltr'
			));
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="permissionAddForm" class="btn btn-success"><?php echo translator::trans("add"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo translator::trans("dissuasion") ?></button>
	</div>
</div>
<?php
require_once('footer.php');
