<?php
$this->the_header();
use \packages\userpanel;
use \packages\userpanel\date;
use \packages\base\translator;
?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-user-secret"></i> <?php echo translator::trans('users.logs'); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link tooltips" title="<?php echo translator::trans('log.search'); ?>" data-toggle="modal" href="#logs-search"><i class="fa fa-search"></i></a>
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<?php
						$hasButtons = $this->hasButtons();
						?>
						<thead>
							<tr>
								<th class="center">#</th>
								<th><?php echo translator::trans('log.title'); ?></th>
								<?php if($this->multiuser){ ?>
									<th><?php echo translator::trans('log.user'); ?></th>
								<?php } ?>
								<th><?php echo translator::trans('log.time'); ?></th>
								<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($this->getLogs() as $log){
								$this->setButtonParam('view', 'link', userpanel\url("logs/view/".$log->id));
								$this->setButtonParam('delete', 'link', userpanel\url("logs/delete/".$log->id));
							?>
							<tr>
								<td class="center"><?php echo $log->id; ?></td>
								<td><?php echo $log->title; ?></td>
								<?php if($this->multiuser){ ?>
									<td><a href="<?php echo userpanel\url("users", ['id' => $log->user->id]); ?>" class="tootips" title="#<?php echo $log->user->id; ?>"><?php echo $log->user->getFullName(); ?></a></td>
								<?php } ?>
								<td class="ltr"><?php echo date::format("Y/m/d H:i:s", $log->time); ?></td>
								<?php
								if($hasButtons){
									echo("<td class=\"center\">".$this->genButtons()."</td>");
								}
								?>
							</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
				<?php $this->paginator(); ?>
			</div>
		</div>
	</div>
	</div>
	<div class="modal fade" id="logs-search" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo translator::trans('search'); ?></h4>
	</div>
	<div class="modal-body">
		<form id="userLogsSearch" action="<?php echo userpanel\url("logs/search"); ?>" method="GET" class="form-horizontal">
			<?php
			$this->setHorizontalForm('sm-3','sm-9');
			$feilds = [
				[
					'label' => translator::trans('log.id'),
					'name' => 'id'
				],
				[
					'label' => translator::trans('log.title'),
					'name' => 'title'
				],
				[
					'label' => translator::trans('log.timeFrom'),
					'name' => 'timeFrom',
					'placeholder' => date::format("Y/m/d", date::time()),
					'ltr' => true
				],
				[
					'label' => translator::trans('log.timeUntil'),
					'name' => 'timeUntil',
					'placeholder' => date::format("Y/m/d", date::time()),
					'ltr' => true
				],
				[
					'type' => 'select',
					'label' => translator::trans('search.comparison'),
					'name' => 'comparison',
					'options' => $this->getComparisonsForSelect()
				]
			];
			if($this->multiuser){
				$userSearch = [
					[
						'name' => 'user',
						'type' => 'hidden'
					],
					[
						'name' => 'user_name',
						'label' => translator::trans("log.user")
					]
				];
				array_splice($feilds, 2, 0, $userSearch);
			}
			foreach($feilds as $input){
				$this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="userLogsSearch" class="btn btn-success">جستجو</button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">انصراف</button>
	</div>
</div>
<?php
$this->the_footer();
