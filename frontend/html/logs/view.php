<?php
use packages\userpanel\Date;
$this->the_header();
?>
<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-exclamation-circle"></i>
				<?php echo t("log.information"); ?>
				<div class="panel-tools">
					<a href="#" class="btn btn-xs btn-link panel-collapse collapses"></a>
				</div>
			</div>
			<div class="panel-body form-horizontal">
				<div class="form-group"><label class="col-xs-4 control-label"><?php echo t("log.ip"); ?>: </label>
					<div class="col-xs-8 ltr"><?php echo $this->log->ip; ?></div>
				</div>
				<div class="form-group"><label class="col-xs-4 control-label"><?php echo t("log.time"); ?>: </label>
					<div class="col-xs-8">
						<span class="tooltips" title="<?php echo Date::format("Q QTS", $this->log->time); ?>"><?php echo date::format("QQQQ", $this->log->time); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-exclamation-circle"></i>
				<?php echo t("log.user"); ?>
				<div class="panel-tools">
					<a href="#" class="btn btn-xs btn-link panel-collapse collapses"></a>
				</div>
			</div>
			<div class="panel-body form-horizontal">
				<div class="form-group"><label class="col-xs-4 control-label"><?php echo t("log.user"); ?>: </label>
					<div class="col-xs-8"><?php echo $this->log->user->getFullName(); ?></div>
				</div>
				<div class="form-group"><label class="col-xs-4 control-label"><?php echo t("log.title"); ?>: </label>
					<div class="col-xs-8">
						<?php echo $this->log->title; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
echo $this->handler->generateRows();
$this->the_footer();
