<?php
$this->the_header();
use packages\userpanel;
use themes\clipone\utility;
use packages\userpanel\user\Api;
use packages\base\{view\error, json};
?>
<div class="row">
<?php if ($this->canAdd) { ?>
	<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-plus"></i> <?php echo t("userpanel.apis.add"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
				<form id="apis-add" method="POST">
					<div class="row">
						<div class="col-xs-12">
						<?php $this->createField(array(
							"type" => "select",
							"name" => "app",
							"label" => t("userpanel.api.app"),
							"options" => $this->getAppsForSelect(),
						)); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
						<?php $this->createField(array(
							"name" => "token",
							"label" => t("userpanel.api.token"),
							"ltr" => true,
							"input-group" => array(
								"right" => array(
									array(
										"type" => "button",
										"class" => "btn btn-default btn-generate-apis-token",
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
							"label" => t("userpanel.api.user"),
						)); ?>
						</div>
					</div>
				<?php } ?>
					<div class="row">
						<div class="col-xs-12">
						<?php $this->createField(array(
							"type" => "select",
							"name" => "status",
							"label" => t("userpanel.api.status"),
							"options" => $this->getStatusForSelect(),
						)); ?>
						</div>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<div class="row">
					<div class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6 col-sm-8 col-sm-offset-2 col-xs-12">
						<button type="submit" form="apis-add" class="btn btn-success btn-submit btn-block">
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
				<i class="fa fa-id-card-o"></i> <?php echo t("userpanel.apis"); ?>
				<div class="panel-tools">
					<a class="btn btn-xs btn-link tooltips" title="<?php echo t("userpanel.search"); ?>" data-toggle="modal" href="#apis-search"><i class="fa fa-search"></i></a>
					<a class="btn btn-xs btn-link panel-collapse collapses" href="#"></a>
				</div>
			</div>
			<div class="panel-body">
			<?php if ($apis = $this->getApis()) { ?>
				<div class="table-responsive">
					<table class="table table-hover table-apis">
					<?php $hasButtons = $this->hasButtons(); ?>
						<thead>
							<tr>
								<th class="center">#</th>
								<th><?php echo t("userpanel.api.app"); ?></th>
							<?php if($this->multiuser){ ?>
								<th><?php echo t("userpanel.api.user"); ?></th>
							<?php } ?>
								<th><?php echo t("userpanel.api.token"); ?></th>
								<th><?php echo t("userpanel.api.status"); ?></th>
							<?php if($hasButtons){ ?><th></th><?php } ?>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($apis as $api) { ?>
							<tr data-api='<?php echo json\encode($api->toArray(true)); ?>'>
								<td class="center"><?php echo $api->id; ?></td>
							<?php if($this->multiuser){ ?>
								<td><a href="<?php echo userpanel\url("settings/apps", array("id" => $api->app->id)); ?>" class="tootips" title="#<?php echo $api->app->id; ?>" target="_blank"><?php echo $api->app->name; ?></a></td>
								<td><a href="<?php echo userpanel\url("users", array("id" => $api->user->id)); ?>" class="tootips" title="#<?php echo $api->user->id; ?>" target="_blank"><?php echo $api->user->getFullName(); ?></a></td>
							<?php } else { ?>
								<td><?php echo $api->app->name; ?></td>
							<?php } ?>
								<td class="ltr"><?php echo $api->token; ?></td>
								<?php
								$statusClass = utility::switchcase($api->status, array(
									"label label-success" => Api::active,
									"label label-inverse" => Api::disable,
								));
								$statusTxt = utility::switchcase($api->status, array(
									"userpanel.api.status.active" => Api::active,
									"userpanel.api.status.disable" => Api::disable,
								));
								?>
								<td><span class="<?php echo $statusClass; ?>"><?php echo t($statusTxt); ?></span></td>
							<?php
							if ($hasButtons) {
								echo("<td class=\"center\">".$this->genButtons(array("apis_edit", "apis_delete"))."</td>");
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
					<?php echo t("error.userpanel.apis.notfound"); ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="apis-search" tabindex="-1" data-show="true" role="diaapi">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo t("userpanel.search"); ?></h4>
	</div>
	<div class="modal-body">
		<form id="apisSearch" action="<?php echo userpanel\url("settings/apis"); ?>" method="GET" class="form-horizontal">
			<?php
			$this->setHorizontalForm("sm-3","sm-9");
			$feilds = array(
				array(
					"label" => t("userpanel.api.id"),
					"name" => "id",
					"ltr" => true,
				),
				array(
					"type" => "select",
					"label" => t("userpanel.api.app"),
					"name" => "app",
					"options" => array_merge(array(
						array(
							"title" => t("userpanel.choose"),
							"value" => "",
						),
					), $this->getAppsForSelect()),
				),
				array(
					"type" => "select",
					"name" => "status",
					"label" => t("userpanel.api.status"),
					"options" => array_merge(array(
						array(
							"title" => t("userpanel.choose"),
							"value" => "",
						),
					), $this->getStatusForSelect()),
				),
				array(
					"label" => t("userpanel.api.token"),
					"name" => "token",
					"ltr" => true,
				),
				array(
					"type" => "select",
					"label" => t("search.comparison"),
					"name" => "comparison",
					"options" => $this->getComparisonsForSelect(),
				),
			);
			if ($this->multiuser) {
				$userSearch = array(
					array(
						"name" => "search_user",
						"type" => "hidden"
					),
					array(
						"name" => "search_user_name",
						"label" => t("userpanel.api.user")
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
		<button type="submit" form="apisSearch" class="btn btn-success"><?php echo t("userpanel.search"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo t("userpanel.cancel"); ?></button>
	</div>
</div>
<div class="modal fade" id="api-delete" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo t("userpanel.apis.delete"); ?> #<span class="api-id"></span></h4>
	</div>
	<div class="modal-body">
		<form id="delete-apis">
			<div class="alert alert-warning">
				<h4 class="alert-heading"> <i class="fa fa-exclamation-triangle"></i> <?php echo t("error." . error::WARNING . ".title"); ?> </h4>
			<?php echo t("userpanel.apis.delete.warning"); ?>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="delete-apis" class="btn btn-danger">حذف</button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">انصراف</button>
	</div>
</div>
<div class="modal fade" id="api-edit" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo t("userpanel.apis.edit"); ?> #<span class="api-id"></span></h4>
	</div>
	<div class="modal-body">
		<form id="edit-apis" method="POST" class="form-horizontal">
			<div class="row">
				<div class="col-xs-12">
				<?php $this->createField(array(
					"type" => "select",
					"name" => "edit_app",
					"label" => t("userpanel.api.app"),
					"options" => $this->getAppsForSelect(),
				)); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
				<?php $this->createField(array(
					"name" => "edit_token",
					"label" => t("userpanel.api.token"),
					"ltr" => true,
					"input-group" => array(
						"right" => array(
							array(
								"type" => "button",
								"class" => "btn btn-default btn-generate-apis-token",
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
					"name" => "edit_user",
					"type" => "hidden",
				));
				$this->createField(array(
					"name" => "edit_user_name",
					"label" => t("userpanel.api.user"),
				)); ?>
				</div>
			</div>
		<?php } ?>
			<div class="row">
				<div class="col-xs-12">
				<?php $this->createField(array(
					"type" => "select",
					"name" => "edit_status",
					"label" => t("userpanel.api.status"),
					"options" => $this->getStatusForSelect(),
				)); ?>
				</div>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="edit-apis" class="btn btn-teal"><?php echo t("userpanel.edit"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo t("userpanel.cancel"); ?></button>
	</div>
</div>
<?php
$this->the_footer();
