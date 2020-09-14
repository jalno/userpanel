<?php
$this->the_header();
use packages\base;
use packages\base\{Translator};
use packages\userpanel;
use packages\userpanel\{Date};
$isRTL = Translator::getLang()->isRTL();
$logs = $this->getLogs();
?>
<div class="panel panel-default panel-logs">
	<div class="panel-heading">
		<i class="fa fa-user-secret"></i> <?php echo t('users.logs'); ?>
		<div class="panel-tools">
			<a class="btn btn-xs btn-link tooltips" title="<?php echo t('log.search'); ?>" data-toggle="modal" href="#logs-search"><i class="fa fa-search"></i></a>
			<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
		</div>
	</div>
	<div class="panel-body">
	<?php if ($logs) { ?>
		<div class="table-responsive">
			<table class="table table-hover table-logs">
			<?php $hasButtons = $this->hasButtons(); ?>
				<thead>
					<tr>
						<th class="center">#</th>
						<th><?php echo t('log.title'); ?></th>
					<?php if($this->multiuser){ ?>
						<th><?php echo t('log.user'); ?></th>
					<?php } ?>
						<th><?php echo t('log.time'); ?></th>
						<?php if ($hasButtons) { ?><th></th><?php } ?>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($logs as $log) { ?>
					<tr>
						<td class="center"><?php echo $log->id; ?></td>
						<td><?php echo $log->title; ?></td>
					<?php if ($this->multiuser) { ?>
						<td<?php echo !$log->user ? ' class="text-center"' : ""; ?>>
						<?php if ($log->user) { ?>
							<a href="<?php echo userpanel\url("users", ['id' => $log->user->id]); ?>" class="tooltips" title="#<?php echo $log->user->id; ?>"><?php echo $log->user->getFullName(); ?></a>
						<?php } else { ?>
							<span class="label label-warning"><?php echo t("userpanel.logs.user.system_log"); ?></span></td>
						<?php } ?>
						</td>
					<?php } ?>
						<td class="<?php echo ($isRTL) ? "ltr" : "rtl" ?>"><?php echo Date::format("Q QTS", $log->time); ?>
					<?php
					if ($hasButtons) {
						$this->setButtonParam('view', 'link', userpanel\url("logs/view/".$log->id));
						$this->setButtonParam('delete', 'link', userpanel\url("logs/delete/".$log->id));
						echo("<td class=\"center\">" . $this->genButtons() . "</td>");
					}
					?>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	<?php
		$this->paginator();
	} else {
	?>
		<div class="alert alert-info">
			<h4 class="alert-heading">
				<i class="fa fa-info-circle"></i>
			<?php echo t("error.notice.title"); ?>
			</h4>
		<?php echo t("userpanel.logs.empty"); ?>
		</div>
	<?php } ?>
	</div>
</div>
<div class="modal fade" id="logs-search" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo t('search'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="userLogsSearch" action="<?php echo userpanel\url("logs/search"); ?>" method="GET" class="form-horizontal">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = [
				[
					'label' => t('log.id'),
					'name' => 'id'
				],
				[
					'label' => t('log.title'),
					'name' => 'title'
				],
				[
					'label' => t('log.ip'),
					'name' => 'ip',
					'ltr' => true,
				],
				[
					'label' => t('log.timeFrom'),
					'name' => 'timeFrom',
					'placeholder' => date::format("Q", date::time()),
					'ltr' => true
				],
				[
					'label' => t('log.timeUntil'),
					'name' => 'timeUntil',
					'placeholder' => date::format("Q", date::time()),
					'ltr' => true
				],
				[
					'type' => 'select',
					'label' => t('search.comparison'),
					'name' => 'comparison',
					'options' => $this->getComparisonsForSelect()
				]
			];
			if ($this->multiuser) {
				$userSearch = [
					[
						'name' => 'user',
						'type' => 'hidden',
					],
					[
						'name' => 'user_name',
						'label' => t("log.user"),
					],
				];
				if ($this->hasAccessToSystemLogs) {
					$userSearch[1]["input-group"] = array(
						"right" => array(
							array(
								"type" => "checkbox",
								"label" => t("userpanel.logs.user.system_log") . ' <i class="fa fa-server" aria-hidden="true"></i>',
								"name" => "system_logs",
								"value" => true,
								"class" => "system-logs",
							),
						),
					);
				}
				array_splice($feilds, 2, 0, $userSearch);
			}
			foreach($feilds as $input){
				$this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="userLogsSearch" class="btn btn-success"><?php echo t("userpanel.search"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo t("userpanel.cancel"); ?></button>
	</div>
</div>
<?php
$this->the_footer();
