<?php
use \packages\base\translator;
use \packages\userpanel;
$this->the_header();
?>
<div class="row">
	<div class="col-sm-12">
		<div class="tabbable">
			<ul class="nav nav-tabs tab-padding tab-space-3 tab-blue">
				<li><a  href="<?php echo userpanel\url('profile/view'); ?>"><?php echo translator::trans("profile.view"); ?></a></li>
				<li><a href="<?php echo userpanel\url('profile/edit'); ?>"><?php echo translator::trans("profile.edit"); ?></a></li>
				<li class="active"><a href="<?php echo userpanel\url('profile/settings'); ?>"><?php echo translator::trans("profile.settings"); ?></a></li>
			</ul>
			<div class="tab-content">
				<div id="settings_panel" class="tab-pane active">
					<form action="<?php echo userpanel\url('profile/settings'); ?>" method="POST" role="form" id="settings_form">
						<div class="row">
							<div class="col-md-12">
								<h3><?php echo translator::trans('profile.settings'); ?></h3>
								<hr>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
							<?php
							foreach($this->getSettings() as $tuning){
								echo("<div class=\"tuningfields tuning-{$tuning->getName()}\">");
								foreach($tuning->getFields() as $field){
									$this->createField($field);
								}
								echo("</div>");
							}
							?>
							</div>
						</div>
						<div class="row" style="margin-top: 20px;margin-bottom: 20px;">
							<div class="col-md-offset-4 col-md-4">
								<button class="btn btn-success btn-block" type="submit"><i class="fa fa-arrow-circle-left"></i> <?php echo translator::trans("user.profile.save"); ?></button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
