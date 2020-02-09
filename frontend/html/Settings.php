<?php
use packages\userpanel;
$this->the_header();
?>
<form id="general-settings-form" class="form-horizontal" action="<?php echo userpanel\url('settings'); ?>" method="POST">
<?php
$this->setHorizontalForm("md-4 sm-5", "md-8 sm-7");
foreach ($this->getSettings() as $tuning) {
	echo '<div class="settings-row">';
	foreach ($tuning->getFields() as $field) {
		echo '<div class="settings-row-item">';
		$this->createField($field);
		echo "</div>";
	}
	echo "</div>";
}
?>
	<div class="row">
		<div class="col-sm-4 col-sm-offset-8">
			<button class="btn btn-success btn-block" type="submit">
				<i class="fa fa-check-square-o"></i>
			<?php echo t("user.profile.save"); ?>
			</button>
		</div>
	</div>
</form>

<?php
$this->the_footer();