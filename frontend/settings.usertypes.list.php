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
				<i class="fa fa-users"></i> <?php echo translator::trans('usertypes'); ?>
				<div class="panel-tools">
					<?php if($this->canAdd){ ?><a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('usertype.add'); ?>" href="<?php echo userpanel\url('settings/usertypes/add'); ?>"><i class="clip-user-plus"></i></a><?php } ?>
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
								<th>#</th>
								<th><?php echo translator::trans('usertype.title'); ?></th>
								<th><?php echo translator::trans('usertype.permissions'); ?></th>
								<th><?php echo translator::trans('usertype.priority'); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->getUserTypes() as $usertype){
								$this->setButtonParam('edit', 'link', userpanel\url("settings/usertypes/edit/".$usertype->id));
								$this->setButtonParam('delete', 'link', userpanel\url("settings/usertypes/delete/".$usertype->id));
							?>
							<tr>
								<td><?php echo $usertype->id; ?></td>
								<td><?php echo $usertype->title; ?></td>
								<td><div class="badge"><?php echo count($usertype->permissions); ?></div></td>
								<td><div class="badge"><?php echo count($usertype->children); ?></div></td>
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
<?php
require_once('footer.php');
