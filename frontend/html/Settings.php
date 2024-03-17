<?php

/** @var \themes\clipone\views\Settings $this */

use packages\userpanel;

$this->the_header();
$this->setHorizontalForm("md-4 sm-5", "md-8 sm-7");
?>

<div class="tabbable">
	<ul class="nav nav-tabs" role="tablist">
		<?php
		$index = 0;
		foreach ($this->getSettings() as $tuning) {
			$name = t("general_settings.{$tuning->getName()}")
				?: t("usertype.permissions.{$tuning->getName()}")
				?: $tuning->getName();
			$icon = $tuning->getIcon();

			echo '<li role="presentation" class="nav-item ' . ($index++ == 0 ? 'active' : '') . '">
				<a href="#settings-' . $tuning->getName() . '" class="nav-link" data-toggle="tab" role="tab">' .
					($icon ? "<i class=\"{$icon}\" aria-hidden=\"true\"></i> " : '') . $name .
				'</a>
			</li>';
		}
		?>
	</ul>
	<form id="general-settings-form" class="form-horizontal" action="<?php echo userpanel\url('settings'); ?>" method="POST">
		<div class="tab-content">
			<?php
			$index = 0;
			foreach ($this->getSettings() as $tuning) {
			?>
				<div id="settings-<?php echo $tuning->getName(); ?>" role="tabpanel" class="tab-pane <?php echo $index++ == 0 ? 'active' : ''; ?>">
					<?php foreach ($tuning->getFields() as $field) { ?>
						<div class="settings-row-item"><?php $this->createField($field); ?></div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>

		<div class="row mt-30">
			<div class="col-sm-4 col-sm-offset-8">
				<button class="btn btn-success btn-block" type="submit">
					<i class="fa fa-check-square-o"></i>
					<?php echo t("user.profile.save"); ?>
				</button>
			</div>
		</div>
	</form>
</div>


<?php
$this->the_footer();
