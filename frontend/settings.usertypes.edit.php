<?php
require_once("header.php");
use \packages\userpanel;
use \packages\userpanel\user;
use \packages\base\translator;
use \themes\clipone\utility;
$usertype = $this->getUserType();
?>
<form action="<?php echo(userpanel\url("settings/usertypes/edit/".$usertype->id)); ?>" method="post">
	<div class="row">
		<div class="col-md-6">
			<?php
			$this->createField(array(
				'name' => 'title',
				'label' => translator::trans('usertype.title')
			));
			?>
		</div>
	</div>
	<div class="row">
		<!-- start: CONDENSED TABLE PANEL -->
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-check-square-o"></i> <?php echo translator::trans('usertype.permissions'); ?>
					<div class="panel-tools">
						<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
					</div>
				</div>
				<div class="panel-body">
					<table class="table table-condensed table-hover">
						<thead>
							<tr>
								<th></th>
								<th><?php echo translator::trans('permission.title'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->getPermissions() as $permission){
							?>
							<tr>
								<td class="center">
									<div class="checkbox-table">
										<label>
											<input type="checkbox" class="flat-grey" name="permissions[]" value="<?php echo $permission;?>"<?php echo ($usertype->hasPermission($permission) ? ' checked' : ''); ?>>
										</label>
									</div>
								</td>
								<td><?php echo $this->translatePermission($permission); ?></td>
							</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-group"></i> <?php echo translator::trans('usertype.periorities'); ?>
					<div class="panel-tools">
						<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
					</div>
				</div>
				<div class="panel-body">
					<table class="table table-condensed table-hover">
						<thead>
							<tr>
								<th></th>
								<th class="hidden-xs"><?php echo translator::trans('usertype.child'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->getChildrenTypes() as $priority){
							?>
								<tr>
									<td class="center">
										<div class="checkbox-table">
											<label>
												<input type="checkbox" class="flat-grey" name="priorities[]" value="<?php echo $priority->id;?>"<?php echo ($this->hasPriority($priority) ? ' checked' : ''); ?>>
											</label>
										</div>
									</td>
									<td><?php echo $priority->title; ?></td>
								</tr>
								<?php
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
				<button class="btn btn-teal btn-block" type="submit"><i class="fa fa-arrow-circle-left"></i> <?php echo translator::trans('usertype.edit'); ?></button>
			</div>
		</div>
	</div>
</form>
<?php
require_once('footer.php');
