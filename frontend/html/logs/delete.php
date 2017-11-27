<?php
$this->the_header();
use \packages\userpanel;
use \packages\userpanel\date;
use \packages\base\translator;
?>
<div class="row">
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-external-link-square"></i> <?php echo translator::trans("log.information"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body form-horizontal">
				<div class="form-group">
					<label class="col-xs-3 control-label"><?php echo translator::trans("log.user"); ?>: </label>
					<div class="col-xs-9"><a href="<?php echo userpanel\url('users', ["id" => $this->log->user->id]); ?>"><?php echo $this->log->user->getFullName(); ?></a></div>
				</div>
				<div class="form-group">
					<label class="col-xs-3 control-label"><?php echo translator::trans("log.ip"); ?>: </label>
					<div class="col-xs-9 ltr"><?php echo $this->log->ip; ?></a></div>
				</div>
				<div class="form-group">
					<label class="col-xs-3 control-label"><?php echo translator::trans("log.title"); ?>: </label>
					<div class="col-xs-9"><?php echo $this->log->title; ?></a></div>
				</div>
				<div class="form-group">
					<label class="col-xs-5 control-label"><?php echo translator::trans("log.time"); ?>: </label>
					<div class="col-xs-7 ltr"><?php echo date::format('Y/m/d H:i:s', $this->log->time); ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-8">
		<div class="panel panel-danger">
			<div class="panel-heading">
				<i class="fa fa-trash"></i> <?php echo translator::trans('log.delete'); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<form action="<?php echo userpanel\url('logs/delete/'.$this->log->id); ?>" method="POST" role="form" class="delete_form">
					<div class="row">
						<div class="col-xs-12">
							<div class="alert alert-block alert-warning fade in">
								<h4 class="alert-heading">
									<i class="fa fa-exclamation-triangle"></i>
									<?php echo translator::trans('attention'); ?>!
								</h4>
								<p>
									<?php echo translator::trans("userpanel.log.delete.warning"); ?>
								</p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="btn-group btn-group-justified" role="group">
								<div class="btn-group" role="group">
									<a href="<?php echo userpanel\url('logs/view/'.$this->log->id); ?>" class="btn btn-default">
										<i class="fa fa-chevron-circle-right"></i>
										<?php echo translator::trans("return"); ?>
									</a>
								</div>
								<div class="btn-group" role="group">
									<button type="submit" class="btn btn-danger">
										<i class="fa fa-trash"></i>
										<?php echo translator::trans("logs.delete"); ?>
									</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
$this->the_footer();
