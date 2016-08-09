<?php
require_once("header.php");
use \packages\userpanel;
use \packages\userpanel\user;
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
								<td><?php echo $row->name; ?></td>
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
			</div>
		</div>
		<!-- end: BASIC TABLE PANEL -->
	</div>
</div>
<?php
require_once('footer.php');
