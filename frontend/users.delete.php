<?php
require_once("header.php");
use \packages\userpanel;
use \packages\base;
use \packages\base\translator;
?>
<!-- start: PAGE CONTENT -->
<div class="row">
	<div class="col-sm-12">
		<form action="<?php echo userpanel\url('users/delete/'.$this->getDataForm('id')); ?>" method="POST" role="form" id="delete_form" class="form-horizontal">
			<div class="alert alert-block alert-warning fade in">
				<h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> <?php echo translator::trans('user.delete.warning.title'); ?>!</h4>
				<p>
					<?php echo translator::trans("user.delete.warning", array(
						'user.id' => $this->getDataForm('id'),
						'user.name' => $this->getDataForm('name'),
						'user.email' => $this->getDataForm('email')
					)); ?>
				</p>
				<p>
					<a href="<?php echo userpanel\url('users'); ?>" class="btn btn-light-grey"><i class="fa fa-chevron-circle-right"></i> بازگشت</a>
					<button type="submit" class="btn btn-yellow"><i class="fa fa-trash-o"></i> حذف</button>
				</p>
			</div>
		</form>

	</div>
</div>
<?php
require_once('footer.php');
