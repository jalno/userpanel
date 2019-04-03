<?php
$this->the_header();
use packages\userpanel;
use packages\base\view\error;
?>
<div class="row">
<?php if ($this->canAdd) { ?>
	<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-plus"></i> <?php echo t("userpanel.apps.add"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<form id="apps-add" method="POST">
					<div class="row">
						<div class="col-xs-12">
						<?php $this->createField(array(
							"name" => "name",
							"label" => t("userpanel.app.name"),
						)); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
						<?php $this->createField(array(
							"name" => "token",
							"label" => t("userpanel.app.token"),
							"ltr" => true,
							"input-group" => array(
								"right" => array(
									array(
										"type" => "button",
										"class" => "btn btn-default btn-generate-apps-token",
										"text" => t("userpanel.create"),
									),
								),
							),
						)); ?>
						</div>
					</div>
				<?php if ($this->multiuser) { ?>
					<div class="row">
						<div class="col-xs-12">
						<?php $this->createField(array(
							"name" => "user",
							"type" => "hidden",
						));
						$this->createField(array(
							"name" => "user_name",
							"label" => t("userpanel.app.user"),
						)); ?>
						</div>
					</div>
				<?php } ?>
				</form>
			</div>
			<div class="panel-footer">
				<div class="row">
					<div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-8 col-sm-offset-2 col-xs-12">
						<button type="submit" form="apps-add" class="btn btn-success btn-submit btn-block">
							<div class="pull-right"> <i class="fa fa-pluse" style="vertical-align: middle;"></i> </div>
						<?php echo t("userpanel.add"); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
	<div class="<?php echo $this->canAdd ? "col-lg-8 col-md-12 col-sm-12 " : ""; ?>col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-id-card-o"></i> <?php echo t("userpanel.apps"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link tooltips" title="<?php echo t("userpanel.search"); ?>" data-toggle="modal" href="#apps-search"><i class="fa fa-search"></i></a>
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
			<?php if ($apps = $this->getApps()) { ?>
				<div class="table-responsive">
					<table class="table table-hover table-apps">
					<?php $hasButtons = $this->hasButtons(); ?>
						<thead>
							<tr>
								<th class="center">#</th>
								<th><?php echo t("userpanel.app.name"); ?></th>
							<?php if($this->multiuser){ ?>
								<th><?php echo t("userpanel.app.user"); ?></th>
							<?php } ?>
								<th><?php echo t("userpanel.app.token"); ?></th>
							<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
						<?php foreach($apps as $app) { ?>
							<tr data-app-id="<?php echo $app->id; ?>">
								<td class="center"><?php echo $app->id; ?></td>
								<td><?php echo $app->name; ?></td>
							<?php if($this->multiuser){ ?>
								<td><a href="<?php echo userpanel\url("users", array("id" => $app->user->id)); ?>" class="tootips" title="#<?php echo $app->user->id; ?>" target="_blank"><?php echo $app->user->getFullName(); ?></a></td>
							<?php } ?>
								<td class="ltr"><?php echo $app->token; ?></td>
							<?php
							if ($hasButtons) {
								echo("<td class=\"center\">".$this->genButtons(array("apps_delete"))."</td>");
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
					<h4 class="alert-heading"><i class="fa fa-info-circle"></i> <?php echo t("error." . error::NOTICE . ".title"); ?></h4>
					<?php echo t("error.userpanel.apps.notfound"); ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="apps-search" tabindex="-1" data-show="true" role="diaapp">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo t("userpanel.search"); ?></h4>
	</div>
	<div class="modal-body">
		<form id="appsSearch" action="<?php echo userpanel\url("settings/apps"); ?>" method="GET" class="form-horizontal">
			<?php
			$this->setHorizontalForm("sm-3","sm-9");
			$feilds = array(
				array(
					"label" => t("userpanel.app.id"),
					"name" => "id",
					"ltr" => true,
				),
				array(
					"label" => t("userpanel.app.name"),
					"name" => "title",
				),
				array(
					"label" => t("userpanel.app.token"),
					"name" => "token",
					"ltr" => true,
				),
				array(
					"type" => "select",
					"label" => t("search.comparison"),
					"name" => "comparison",
					"options" => $this->getComparisonsForSelect()
				)
			);
			if ($this->multiuser) {
				$userSearch = array(
					array(
						"name" => "search_user",
						"type" => "hidden"
					),
					array(
						"name" => "search_user_name",
						"label" => t("userpanel.app.user")
					)
				);
				array_splice($feilds, 2, 0, $userSearch);
			}
			foreach ($feilds as $input) {
				$this->createField($input);
			}
			?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="appsSearch" class="btn btn-success"><?php echo t("userpanel.search"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo t("userpanel.cancel"); ?></button>
	</div>
</div>
<div class="modal fade" id="app-delete" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo t("userpanel.apps.delete"); ?> #<span class="app-id"></span></h4>
	</div>
	<div class="modal-body">
		<form id="delete-apps">
			<div class="alert alert-warning">
				<h4 class="alert-heading"> <i class="fa fa-exclamation-triangle"></i> <?php echo t("error." . error::WARNING . ".title"); ?> </h4>
			<?php echo t("userpanel.apps.delete.warning"); ?>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="delete-apps" class="btn btn-danger">حذف</button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">انصراف</button>
	</div>
</div>
<?php
$this->the_footer();
