<?php
require_once dirname(__FILE__) . '/../../Parts/header/header.php';

use packages\userpanel;

$usertype = $this->getUserType();

?>
<form class="edit-usertype" action="<?php echo userpanel\url('settings/usertypes/edit/'.$usertype->id); ?>" method="POST">
	<div class="row">
		<div class="col-md-6">
			<?php
            $this->createField([
                'name' => 'title',
                'label' => t('usertype.title'),
            ]);
?>
		</div>
	</div>
	<div class="row">
		<!-- start: CONDENSED TABLE PANEL -->
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-check-square-o"></i> <?php echo t('usertype.permissions'); ?>
					<div class="panel-tools">
						<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
					</div>
				</div>
				<div class="panel-body panel-permissions userpanel-permissions-fancytree-container">

				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-group"></i> <?php echo t('usertype.periorities'); ?>
					<div class="panel-tools">
						<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
					</div>
				</div>
				<div class="panel-body panel-scroll" style="height:300px;">
					<table class="table table-condensed table-hover">
						<thead>
							<tr>
								<th class="hidden-xs"><?php echo t('usertype.child'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
                foreach ($this->getChildrenTypes() as $priority) {
                    ?>
								<tr>
									<td>
										<div class="checkbox checkbox-primary">
											<label>
												<input type="checkbox" class="flat-grey" name="priorities[]" value="<?php echo $priority->id; ?>"<?php echo $this->hasPriority($priority) ? ' checked ' : ''; ?>>
												<?php echo $priority->title; ?>
											</label>
										</div>
									</td>
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
				<button class="btn btn-teal btn-block" type="submit">
					<i class="fa fa-check-square-o"></i>
				<?php echo t('usertype.edit'); ?>
				</button>
			</div>
		</div>
	</div>
</form>
<?php
require_once dirname(__FILE__) . '/../../Parts/footer/footer.php';
