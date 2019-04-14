<?php
require_once("header.php");
use \packages\userpanel;
use \packages\userpanel\{user, authentication};
use \packages\base\translator;
use \themes\clipone\utility;
?>
<div class="row">
	<div class="col-md-12">
		<!-- start: BASIC TABLE PANEL -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-users"></i> <?php echo translator::trans('users'); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('user.add'); ?>" href="<?php echo userpanel\url('users/add'); ?>"><i class="clip-user-plus"></i></a>
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('user.search'); ?>" data-toggle="modal" href="#users-search"><i class="fa fa-search"></i></a>
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
								<th><?php echo translator::trans('user.name'); ?></th>
								<th><?php echo translator::trans('user.type.name'); ?></th>
								<th><?php echo translator::trans('user.email'); ?><br><?php echo translator::trans('user.cellphone'); ?></th>
								<th><?php echo translator::trans('user.status'); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->dataList as $row){
								$this->setButtonParam('view', 'link', userpanel\url("users/view/".$row->id));
								$this->setButtonParam('edit', 'link', userpanel\url("users/edit/".$row->id));
								$this->setButtonParam('delete', 'link', userpanel\url("users/delete/".$row->id));
								$canEdit = false;
								if ($this->types) {
									$canEdit = in_array($row->type->id, $this->types);
								} else {
									$canEdit = authentication::getID() != $row->id;
								}
								$this->setButtonActive("edit", $canEdit);
								$this->setButtonActive("delete", authentication::getID() != $row->id);
								$statusClass = utility::switchcase($row->status, array(
									'label label-inverse' => user::deactive,
									'label label-success' => user::active,
									'label label-warning' => user::suspend
								));
								$statusTxt = utility::switchcase($row->status, array(
									'deactive' => user::deactive,
									'active' => user::active,
									'suspend' => user::suspend
								));
							?>
							<tr>
								<td class="center"><?php echo $row->id; ?></td>
								<td><?php echo $row->getFullName(); ?></td>
								<td><?php echo $row->type->title; ?></td>
								<td><?php echo $row->email; ?><br><?php echo $row->cellphone; ?></td>
								<td class="hidden-xs"><span class="<?php echo $statusClass; ?>"><?php echo translator::trans($statusTxt); ?></span></td>
								<?php
								if($hasButtons){
									echo("<td class=\"center\">".$this->genButtons()."</td>");
								}
								?>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
				<?php $this->paginator(); ?>
			</div>
		</div>
		<!-- end: BASIC TABLE PANEL -->
	</div>
</div>
<div class="modal fade" id="users-search" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('users.search'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="usersearchform" action="<?php echo userpanel\url("users"); ?>" method="GET" class="form-horizontal">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = array(
				array(
					'label' => translator::trans('user.id'),
					'name' => 'id'
				),
				array(
					'label' => translator::trans('user.name'),
					'name' => 'name'
				),
				array(
					'label' => translator::trans('user.lastname'),
					'name' => 'lastname'
				),
				array(
					'label' => translator::trans('user.email'),
					'name' => 'email'
				),
				array(
					'label' => translator::trans('user.cellphone'),
					'name' => 'cellphone'
				),
				array(
					'type' => 'select',
					'label' => translator::trans('user.type'),
					'name' => 'type',
					'options' => $this->getTypesForSelect()
				),
				array(
					'type' => 'checkbox',
					'label' => translator::trans('user.online'),
					'name' => 'online',
					'options' => [array(
						'value' => 1,
						'label' => translator::trans('user.online.yes')
					)]
				),
				array(
					'type' => 'select',
					'label' => translator::trans('user.status'),
					'name' => 'status',
					'options' => $this->getStatusForSelect()
				),
				array(
					'type' => 'select',
					'label' => translator::trans('search.comparison'),
					'name' => 'comparison',
					'options' => $this->getComparisonsForSelect()
				)
			);
			foreach($feilds as $input){
				echo $this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="usersearchform" class="btn btn-success">جستجو</button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">انصراف</button>
	</div>
</div>
<?php
require_once('footer.php');
