<?php $this->the_header(); ?>
<div class="tabbable">
	<ul class="nav nav-tabs tab-padding tab-space-3 tab-blue">
		<?php echo $this->buildTabs(); ?>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active">
		<?php
        $tab = $this->getActiveTab();
if ($tab) {
    $view = $tab->getView();
    if ($view) {
        $view->output();
    }
}
?>
		</div>
	</div>
</div>
<?php
$this->the_footer();
