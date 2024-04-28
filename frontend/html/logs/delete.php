<?php
use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\Authorization;
use packages\userpanel\Date;

$canSearchUsers = Authorization::is_accessed('users_list');
$this->the_header();
?>
<div class="row">
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa  fa-info-circle"></i> <?php echo t('log.information'); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body form-horizontal">
				<div class="form-group">
					<label class="col-xs-3 control-label"><?php echo t('log.user'); ?>: </label>
					<div class="col-xs-9 text">
						<?php
                        if ($this->log->user) {
                            if ($canSearchUsers) {
                                ?>
							<a target="_blank" href="<?php echo userpanel\url('users', ['id' => $this->log->user->id]); ?>"><?php echo $this->log->user->getFullName(); ?></a>
						<?php
                            } else {
                                echo $this->log->user->getFullName();
                            }
                        } else {
                            ?>
							<span class="label label-warning"><?php echo t('userpanel.logs.user.system_log'); ?></span>
						<?php
                        }
?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-3 control-label"><?php echo t('log.ip'); ?>: </label>
					<div class="col-xs-9 text ltr"><?php echo $this->log->ip; ?></a></div>
				</div>
				<div class="form-group">
					<label class="col-xs-3 control-label"><?php echo t('log.title'); ?>: </label>
					<div class="col-xs-9 text"><?php echo $this->log->title; ?></a></div>
				</div>
				<div class="form-group">
					<label class="col-xs-3 control-label"><?php echo t('log.time'); ?>: </label>
					<div class="col-xs-9 text ltr"><?php echo Date::format('Q QTS', $this->log->time); ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-8">
		<div class="panel panel-danger">
			<div class="panel-heading">
				<i class="fa fa-trash"></i> <?php echo t('logs.delete'); ?>
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
									<?php echo t('attention'); ?>!
								</h4>
								<p>
									<?php echo t('userpanel.log.delete.warning'); ?>
								</p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<div class="btn-group btn-group-justified" role="group">
								<div class="btn-group" role="group">
									<a href="<?php echo userpanel\url('logs/view/'.$this->log->id); ?>" class="btn btn-default">
										<i class="fa fa-chevron-circle-<?php echo Translator::getLang()->isRTL() ? 'right' : 'left'; ?>"></i>
										<?php echo t('return'); ?>
									</a>
								</div>
								<div class="btn-group" role="group">
									<button type="submit" class="btn btn-danger">
										<i class="fa fa-trash"></i>
										<?php echo t('logs.delete'); ?>
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
