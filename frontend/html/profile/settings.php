<?php
use packages\base\translator;
use packages\userpanel;
?>
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
			<button class="btn btn-success btn-block" type="submit"><i class="fa fa-check-square-o"></i> <?php echo translator::trans("user.profile.save"); ?></button>
		</div>
	</div>
</form>