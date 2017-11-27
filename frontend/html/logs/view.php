<?php
$this->the_header();
use \packages\userpanel\date;
?>
<div class="row">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-exclamation-circle"></i>
						مشخصات
						<div class="panel-tools">
							<a href="#" class="btn btn-xs btn-link panel-collapse collapses"></a>
						</div>
					</div>
					<div class="panel-body form-horizontal">
						<div class="form-group"><label class="col-xs-4 control-label"> آی پی: </label>
							<div class="col-xs-8 ltr"><?php echo $this->log->ip; ?></div>
						</div>
						<div class="form-group"><label class="col-xs-4 control-label"> زمان: </label>
							<div class="col-xs-8">
								<span class="tooltips" title="<?php echo date::format("Y/m/d H:i:s", $this->log->time); ?>"><?php echo date::format("l j F Y", $this->log->time); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-exclamation-circle"></i>
						کاربر
						<div class="panel-tools">
							<a href="#" class="btn btn-xs btn-link panel-collapse collapses"></a>
						</div>
					</div>
					<div class="panel-body form-horizontal">
						<div class="form-group"><label class="col-xs-4 control-label"> کاربر: </label>
							<div class="col-xs-8"><?php echo $this->log->user->getFullName(); ?></div>
						</div>
						<div class="form-group"><label class="col-xs-4 control-label"> اقدام: </label>
							<div class="col-xs-8">
								<?php echo $this->log->title; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo $this->handler->generateRows(); ?>
	</div>
</div>
<?php
$this->the_footer();
