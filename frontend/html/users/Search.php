<?php

use themes\clipone\Utility;
use packages\userpanel\{User, Authentication};
use function packages\userpanel\url;

$this->the_header();

$users = $this->getDataList();

$hasUsers = !empty($users);
$formData = $this->getFormData();
unset($formData["status"], $formData["page"]);

?>

<?php if ($this->canExport) { ?>
<div class="row">
	<div class="col-lg-3 col-lg-offset-9 col-md-4 col-md-offset-8 col-sm-4 col-sm-offset-8">
		<a class="btn btn-info btn-block btn-export<?php echo !$hasUsers ? ' disabled' : ''; ?>" href="<?php echo $hasUsers ? url("users", array_merge($this->getFormData(), array("download" => "csv"))) : '#'; ?>"<?php echo !$hasUsers ? ' disabled' : ''; ?>>
			<div class="btn-icons"> <i class="fa fa-download"></i> </div>
			<?php echo t("userpanel.users.export.csv"); ?>
		</a>
	</div>
</div>
<?php } ?>
<div class="search-status-tabs-container">
	<div class="row">
		<div class="col-lg-3 col-lg-push-9 col-md-4 col-md-push-8 col-sm-4 col-sm-push-8">
			<div class="row">
				<div class="<?php echo $this->canAdd ? 'col-xs-6' : 'col-xs-12'; ?>">
					<a class="btn btn-default btn-block" data-toggle="modal" href="#users-search">
						<div class="btn-icons"> <i class="fa fa-search"></i> </div>
					<?php echo t("userpanel.search"); ?>
					</a>
				</div>
			<?php if ($this->canAdd) { ?>
				<div class="col-xs-6">
					<a class="btn btn-success btn-block" href="<?php echo url("users/add"); ?>">
						<div class="btn-icons"> <i class="fa fa-user-plus"></i> </div>
					<?php echo t("userpanel.add"); ?>
					</a>
				</div>
			<?php } ?>
			</div>
		</div>
		<div class="col-lg-9 col-lg-pull-3 col-md-8 col-md-pull-4 col-sm-8 col-sm-pull-4">
			<ul class="search-status-tabs" role="tablist">
				<li class="status-tab<?php echo $this->isActiveStatusTab('status', User::active) ? ' active' : ''; ?>">
					<a class="status-tab-link" href="<?php echo url("users", array_merge($formData, ['status' => User::active])); ?>">
					<?php echo t("active"); ?>
					</a>
				</li>
				<li class="status-tab<?php echo $this->isActiveStatusTab('status', User::suspend) ? ' active' : ''; ?>">
					<a class="status-tab-link" href="<?php echo url("users", array_merge($formData, ['status' => User::suspend])); ?>"><?php echo t("suspend"); ?></a>
				</li>
				<li class="status-tab<?php echo $this->isActiveStatusTab('status', User::deactive) ? ' active' : ''; ?>">
					<a class="status-tab-link" href="<?php echo url("users", array_merge($formData, ['status' => User::deactive])); ?>"><?php echo t("deactive"); ?></a>
				</li>
				<li class="status-tab<?php echo $this->isActiveStatusTab('status', "") ? ' active' : ''; ?>">
					<a class="status-tab-link" href="<?php echo url("users", $formData); ?>"><?php echo t("userpanel.all"); ?></a>
				</li>
			</ul>
		</div>
	</div>
</div>

<?php if ($hasUsers) { ?>
	<div class="table-responsive">
		<table class="table table-hover table-users">
		<?php $hasButtons = $this->hasButtons(); ?>
			<thead>
				<tr>
					<th class="center">#</th>
					<th><?php echo t("user.name"); ?></th>
					<th><?php echo t("user.type.name"); ?></th>
					<th><?php echo t("user.email"); ?><br><?php echo t("user.cellphone"); ?></th>
					<th><?php echo t("user.country") . " - " . t("user.city"); ?></th>
					<th><?php echo t("user.status"); ?></th>
				<?php if($hasButtons){ ?>
					<th></th>
				<?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php
			$me = Authentication::getID();
			foreach ($users as $row) {
				$this->setButtonParam("view", "link", url("users/view/" . $row->id));
				$this->setButtonParam("edit", "link", url("users/edit/" . $row->id));
				$this->setButtonParam("delete", "link", url("users/delete/" . $row->id));
				$this->setButtonActive("delete", ($this->canDelete and $row->id != $me));
				$statusClass = Utility::switchcase($row->status, array(
					"label label-inverse" => User::deactive,
					"label label-success" => User::active,
					"label label-warning" => User::suspend
				));
				$statusTxt = Utility::switchcase($row->status, array(
					"user.status.deactive" => User::deactive,
					"user.status.active" => User::active,
					"user.status.suspend" => User::suspend
				));
				$country = "";
				if ($row->country) {
					$country = $row->country->name;
				}
				if ($row->city) {
					$country .= ($country ? " - " : "") . $row->city;
				}
			?>
				<tr>
					<td class="center"><?php echo $row->id; ?></td>
					<td><?php echo $row->getFullName(); ?></td>
					<td><?php echo $row->type->title; ?></td>
					<td><?php echo $row->email; ?><br><?php echo $row->getCellphoneWithDialingCode(); ?></td>
					<td class="center"><?php echo $country ? $country : "-"; ?></td>
					<td><span class="<?php echo $statusClass; ?>"><?php echo t($statusTxt); ?></span></td>
				<?php if ($hasButtons) { ?>
					<td class="center"><?php echo $this->genButtons(); ?></td>
				<?php } ?>
					</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
<?php 
	$this->paginator();
} else {
?>
	<div class="alert alert-info alert-block">
		<h4 class="alert-heading">
			<i class="fa fa-exclamation-circle"></i>
		<?php echo t("error.notice.title"); ?>
		</h4>
	<?php echo t('warning.userpanel.empty_result'); ?>
	</div>
<?php } ?>
</div>
<div class="modal fade" id="users-search" tabindex="-1" data-show="true" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?php echo t("users.search"); ?></h4>
	</div>
	<div class="modal-body">
		<form id="users-search-form" action="<?php echo url("users"); ?>" method="GET" class="form-horizontal">
		<?php
		$this->setHorizontalForm("sm-3","sm-9");
		$fields = array(
			array(
				"label" => t("user.id"),
				"name" => "id",
				"type" => "number",
				"ltr" => true,
				"min" => 1,
			),
			array(
				"label" => t("user.name"),
				"name" => "name"
			),
			array(
				"label" => t("user.lastname"),
				"name" => "lastname"
			),
			array(
				"label" => t("user.email"),
				"name" => "email"
			),
			array(
				"label" => t("user.cellphone"),
				"name" => "cellphone[number]",
				"input-group" => array(
					"first" => array(
						array(
							'type' => 'select',
							'name' => "cellphone[code]",
							'options' => array(),
						),
					),
				),
			),
			array(
				"name" => "type",
				"type" => "hidden",
			),
			array(
				"name" => "type-select",
				"type" => "select",
				"label" => t("user.type"),
				"multiple" => true,
				"value" => $this->getSelectedTypes(),
				"options" => $this->getTypesForSelect(),
			),
			array(
				'name' => 'register',
				'label' => t('userpanel.profile.register_date'),
				'ltr' => true,
			),
			array(
				"type" => "checkbox",
				"label" => t("userpanel.users.search.has_custom_permissions"),
				"name" => "has_custom_permissions",
				"options" => [array(
					"value" => 1,
					"label" => t("yes")
				)]
			),
			array(
				"type" => "checkbox",
				"label" => t("user.online"),
				"name" => "online",
				"options" => [array(
					"value" => 1,
					"label" => t("user.online.yes")
				)],
			),
			array(
				"type" => "select",
				"label" => t("user.status"),
				"name" => "status",
				"options" => $this->getStatusForSelect()
			),
			array(
				"type" => "select",
				"name" => "country",
				"label" => t("user.country"),
				"options" => $this->getCountriesForSelect(),
				"ltr" => true
			),
			array(
				"label" => t("user.city"),
				"name" => "city"
			),
			array(
				"type" => "select",
				"label" => t("search.comparison"),
				"name" => "comparison",
				"options" => $this->getComparisonsForSelect()
			),
		);
		foreach ($fields as $input) {
			$this->createField($input);
		}
		?>
		</form>
	</div>
	<div class="modal-footer">
		<button type="submit" form="users-search-form" class="btn btn-success"><?php echo t("userpanel.search"); ?></button>
		<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo t("userpanel.cancel"); ?></button>
	</div>
</div>
<?php

$this->the_footer();